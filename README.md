CrocosSecurityBundle - README
===============================

概要
------

**CrocosSecurityBundle** はよりシンプルに認証状態の管理を行うためにのSymfony用のバンドルで、複雑な SecurityBundle を置き換えるために開発されました。SecurityBundle と比べ、次のような違いがあります。

- アノテーションのみを用いて設定を行います
- ログイン、ログアウトの状態切り替えは開発者が明示的に行います

インストール方法
-----------------

`PROJECT_ROOT/vendor/bundles/Crocos/SecurityBundle` に配置します。

### app/AppKernel.php

CrocosSecurityBundle を登録します。

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Crocos\SecurityBundle\CrocosSecurityBundle(),

        );
    }

`Symfony\Bundle\SecurityBundle\SecurityBundle` の行は削除します。

### app/config/config.yml

`security.yml` を読み込んでいる行は削除します。


### app/autoload.php

`Crocos` プレフィックスを ClassLoader に登録します。

    $loader->registerNamespaces(array(
        // ...
        'Crocos'  => array(__DIR__.'/../src', __DIR__.'/../vendor/bundles'),
        // ...
    ));


使い方
--------

`Secure` アノテーション、`SecureConfig` アノテーションをコントローラのメソッドもしくはクラスに設定します。

    <?php

    use Crocos\SecurityBundle\Annotation\Secure;
    use Crocos\SecurityBundle\Annotation\SecureConfig;

    /**
     * @SecureConfig(forward="CrocosAppBundle:Security:login")
     */
    class AppController
    {
    }

    class SampleController extends AppController
    {
        /**
         * @Secure
         */
        public function secureAction()
        {
            $user = $this->get('crocos_security.context')->getUser();
        }
    }

    /**
     * @Secure
     */
    class SecurityController extends AppController
    {
        public function login(Request $request)
        {
            $user = $this->findUser($request->request->get('username'), $request->request->get('password'));

            $this->get('crocos_security.context')->login($user);

            return $this->redirect('/');
        }

        public function logout()
        {
            $this->get('crocos_security.context')->logout();

            return $this->redirect('/login');
        }
    }


### Secure アノテーション

`Secure` アノテーションを付与したコントローラは認証が必要として扱います。クラスに設定した場合はすべてのアクションに、メソッドに設定した場合は指定したアクションのみが対象です。

`Secure` アノテーションは次の属性が設定可能です。

#### disabled

- type: boolean
- default: false

trueに設定した場合、認証不要であることを表します。初期値はfalseなので、単に `Secure` アノテーションを設定した場合は認証が必要になります。

#### roles

必要な権限を配列で設定します。現在この値を用いた認可処理は未実装です。

- type: array
- default: []


### SecureConfig アノテーション

`SecureConfig` アノテーションは認証に関する設定を行います。適応可能範囲は `Secure` アノテーションと同じです。

`SecureConfig` アノテーションは次の属性が設定可能です。

#### domain

同一プロジェクト内で異なる認証処理を行わなければならない場合（ユーザ専用ページ、管理者専用ページなど）、認証状況が適応される領域を指定したい場合に指定します。

デフォルトではセッションを用いて認証状態を保持しますが、domainはセッションの名前空間として利用されます。

- type: string
- default: "secured"

#### auth

認証状態の管理方法を指定します。初期値は "session" で、セッションを用いた認証状態の管理を行います。

この他にも、FacebookのPHP-SDKの状態と連動させた "facebook" や、独自の方法を指定することもできます。

- type: string
- default: "session"

#### forward

非ログイン状態で認証が必要なコントローラにアクセスした場合、ここに指定したコントローラが呼び出されます。コントローラのメソッド名（クラス::メソッド）を指定するか、Symfonyの短縮形式（バンドル名:コントローラ名:アクション名）でも指定できます。forwardが指定されていない場合に認証が必要なコントローラにアクセスした場合はエラーになります。

forwardに指定したコントローラへのアクセスは、無限ループを防ぐため、認証が必須と設定されている場合であっても制御は行いません。

- type: string

#### basic

BASIC認証を有効にします。値には「ユーザ名:パスワード」形式の文字列、もしくはその文字列の配列(= 複数ユーザ)を指定します。

    @SecureConfig(domain="secured", basic="user:pass")

認証領域(realm)はdomainの値を元に設定されます。上記の場合は "Secured Area" となります。

- type: string|array


### クラスに設定したアノテーション

`Secure` アノテーションがクラスに設定されている場合はすべてのメソッドに同じ内容が適応されます。親クラスに設定されている値も読み込まれます。親クラス -> 子クラス -> メソッド の順番に読み込まれ、あとに読み込まれた値で上書きされます。

disabled属性を指定しなかった場合は認証が必要として上書きされますが、その他の属性は指定しない限り上書きされません。メソッドのアノテーションが読み込まれた段階で指定されていない場合のみ、デフォルト値が設定されます。


### サンプルコード

次のコードはアノテーションを用いて認証を行うサンプルコードです。

> `CrocosSecurityBundle` を使用する際は、設定をしやすくするためにアプリケーションごとに共通のコントローラクラスを作成することを推奨します。

#### 基本的なサンプル

AppController を継承した ProductController と AccountController が定義されています。ProductController の buyAction には Secure アノテーションが指定されているので認証が必要となります。
AccountController はクラスに Secure アノテーションが指定してあるため、すべてのアクションで認証が必要です。ただし、AppController の SecureConfig アノテーションで loginAction が forward に指定されているため、loginAction は常に認証が不要になります。

    <?php

    namespace Crocos\AppBundle\Controller;

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Crocos\SecurityBundle\Annotation\Secure;
    use Crocos\SecurityBundle\Annotation\SecureConfig;

    /**
     * @SecureConfig(forward="CrocosAppBundle:Accont:login")
     */
    abstract class AppController extends Controller
    {
        protected function getUser()
        {
            return $this->get('crocos_security.context')->getUser();
        }
    }

    /**
     * @Route("/product")
     */
    class ProductController extends AppController
    {
        /**
         * @Route("/{id}", requirements={"id" = "\d+"})
         */
        public function showAction($id)
        {
            // ...
        }

        /**
         * @Secure
         * @Route("/{id}/buy", requirements={"id" = "\d+"})
         */
        public function buyAction($id)
        {
            // ...
        }
    }

    /**
     * @Secure
     */
    class AccountController extends AppController
    {
        /**
         * @Route("/account")
         */
        public function indexAction()
        {
            // ...
        }

        /**
         * @Route("/login")
         * @Template
         */
        public function loginAction(Request $request)
        {
            if ($request->getMethod() === 'POST') {
                $username = $request->request->get('username');
                $password = $request->request->get('password');

                $user = $this->get('doctrine')->getRepository('CrocosAppBundle:User')
                    ->findUser($username, $password);

                $this->get('crocos_security.context')->login($user);

                return $this->redirect('/');
            }

            return array();
        }
    }


#### 管理者用ページ向けのサンプル

管理者用のコントローラを作る場合、次のようにdomain属性を指定して、別の認証領域とします。AppController に Secure アノテーションが指定されているため、すべてのコントローラで認証が必要となります。

    <?php

    namespace Crocos\AppBundle\Controller\Admin;

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Crocos\SecurityBundle\Annotation\Secure;
    use Crocos\SecurityBundle\Annotation\SecureConfig;

    /**
     * @Secure
     * @SecureConfig(domain="admin", forward="CrocosAppBundle:Admin\Accont:login")
     */
    abstract class AppController extends Controller
    {
        protected function getUser()
        {
            return $this->get('crocos_security.context')->getUser();
        }
    }

    /**
     * @Route("/admin")
     */
    class AccountController extends AppController
    {
        /**
         * @Route("/login")
         */
        public function loginAction(Request $request)
        {
            // ...
        }
    }


SecurityContext
-----------------

認証に関わる状態は `crocos_security.context` というキーでサービスコンテナに登録されている、 `Crocos\SecurityBundle\Security\SecurityContext` オブジェクトが保持しています。なお実際の処理内容については後述する **Auth Logic** によって変更可能です。

### ログイン

ログインを行うには、`login()` メソッドにユーザ情報を渡します。

    $user = $userRepository->findUser('Katsuhiro Ogawa');
    $this->get('crocos_security.context')->login($user);


### ログイン状態の確認

ログイン状態の確認は、`isAuthenticated()` メソッドにて行います。

    if ($this->get('crocos_security.context')->isAuthenticated()) {
        echo 'ログインしています';
    }

### ログインしているユーザの取得

ログインしているユーザの取得は、`getUser()` メソッドにて行います。ログインしていない場合は null が返されます。

    $user = $this->get('crocos_security.context')->getUser();

### ログアウト

ログアウトは、`logout()` メソッドで行えます。

    $this->get('crocos_security.context')->logout();

    $this->get('crocos_security.context')->getUser();           // => null
    $this->get('crocos_security.context')->isAuthenticated();   // => false


Auth Logic
--------------

Auth Logic は認証状態の管理方法を切り替える仕組みです。`Secure` アノテーションの `auth` と対応しています。標準では、セッションを用いて認証状態の管理を行う `SessionAuth` (auth="session")、Facebook PHP SDKに状態管理を委譲する `FacebookAuth` (auth="facebook") の2つがあります。また、既存の Auth Logic を拡張したり、独自に作成することも可能です。

### SessionAuth

    @SecureConfig(auth="session")

`SessionAuth` はセッションを用いて認証状態を管理する仕組みです。

### EntityAuth

    @SecureConfig(auth="entity")

`EntityAuth` はログインユーザにエンティティが使用されることを想定したもので、基本的には `SessionAuth` と同等です。`SessionAuth` を用いた場合、ログイン中のユーザ情報はセッションにシリアライズして格納されます。ユーザ情報がオブジェクトの場合、オブジェクトがシリアライズされて保存されます。`EntityAuth` を用いた場合、クラス名とIDのみをセッションへ格納し、アクセスがあるたびにリポジトリからエンティティを取得します。

`EntityAuth` を用いるにあたって、ログイン対象のエンティティには必ずgetId()メソッドを実装する必要があります。この値はセッションからエンティティを復元する際、リポジトリのfind()メソッドに渡されます。また、ログイン中のユーザの有効性を確認したい場合、エンティティにisEnabled()メソッドを実装することで、エンティティ取得後に有効性の確認が可能です。ログインしていてもisEnabled()がfalseであれば、ログアウトされます。

### FacebookAuth

    @SecureConfig(auth="facebook")

`FacebookAuth` はFacebook PHP SDKを用いて認証を行います。

`login()` および `logout()` メソッドは使用できません。`BaseFacebook::getLoginUrl()` を用いて認証してください。

`FacebookAuth` を利用する場合は、`facebook.api` というキーで `Facebook` オブジェクトをDIコンテナにサービス登録してください。

### カスタムAuth Logic

独自の Auth Logic を作成するにはまず、 `Crocos\SecurityBundle\Security\AuthLogic\AuthLogicInterface` インターフェイスを実装したクラスを作成します。Auth Logic には次の5つのメソッドを定義する必要があります。

- setDomain($domain)
- login($user)
- function logout()
- function isAuthenticated()
- function getUser()

`setDomain()` メソッド以外は `SecurityContext` クラスから委譲される形で呼び出されます。`setDomain()` メソッドはアノテーションで読み込まれた `domain` の値が渡されます。

#### カスタムAuth Logicの登録

Auth Logic を作成したら、DIコンテナに登録する必要があります。その際、`crocos_security.auth_logic` タグを付与することで CrocosSecurityBundle に登録可能です。アノテーションには `alias` に記述した値を指定します。

    services:
        myapp.security.my_auth:
            class: Crocos\AppBundle\Security\MyAuth
            tags:
                - { name: crocos_security.auth_logic, alias: my_auth }

上記のAuth Logicを呼び出す場合は次のようになります。

    /**
     * @SecureConfig(auth="my_auth")
     */
    class AppController
    {
    }


AuthException
---------------

任意の場所でログイン画面に遷移したい場合は、 `Crocos\SecurityBundle\Exception\AuthException` オブジェクトをスローします。なお、AuthExceptionのコンストラクタの第2引数にattributes配列を指定でき、ログイン画面へ遷移する際にルーティングのパラメータとして渡されます。

    use Crocos\SecurityBundle\Annotation\Secure;
    use Crocos\SecurityBundle\Annotation\SecureConfig;
    use Crocos\SecurityBundle\Exception\AuthException;

    /**
     * @SecureConfig(forward="CrocosAppBundle:Demo:login")
     */
    class AppController
    {
    }

    class DemoController extends AppController
    {
        /**
         * @Secure
         */
        public function someAction($id)
        {
            if ($this->hasSomeError()) {
                throw new AuthException('Login required', array('id' => $id));
            }
        }

        public function loginAction($id = null)
        {
            // do login
        }
    }


Twig連携
----------

    CrocosSecurityBundleを読み込むとTwigテンプレート内で `_security` 変数が有効になります。`_security` 変数は `SecurityContext` オブジェクトへの参照を持ちます。これを用いてテンプレート内で条件分岐などを行えます。

    {% if _security.isAuthenticated %}
      <p>Logged in as {{ _security.user }}</p>
    {% endif %}
