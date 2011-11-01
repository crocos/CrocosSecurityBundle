CrocosSecurityBundle - README
===============================

概要
------

`CrocosSecurityBundle` はよりシンプルに認証状態の管理を行うためにのバンドルで、複雑なSecurityコンポーネントを置き換えるために開発されました。Securityコンポーネントと比べ、次のような違いがあります。

- アノテーションのみを用いて設定を行います
- ログイン、ログアウトの状態切り替えは開発者が明示的に行います

インストール方法
-----------------

`PROJECT_ROOT/vendor/bundles/Crocos/SecurityBundle` に配置します。

### app/AppKernel.php

`Symfony\Bundle\SecurityBundle\SecurityBundle` は外します。

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Crocos\SecurityBundle\CrocosSecurityBundle(),

        );
    }


### app/autoload.php

`Crocos` プレフィックスをオートローダに登録します。

    $loader->registerNamespaces(array(
        // ...
        'Crocos'  => array(__DIR__.'/../src', __DIR__.'/../vendor/bundles'),
        // ...
    ));


使い方
--------

`Secure` アノテーション、`SecureConfig`アノテーションをコントローラのメソッドもしくはクラスに設定します。

    <?php
    use Crocos\SecurityBundle\Annotation\Secure;

    /**
     * @SecureCnofig(forward="CrocosAppBundle:Security:login")
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


### Secure アノテーション

`Secure` アノテーションを付与したコントローラは認証が必要として扱います。クラスに設定した場合はすべてのアクションに、メソッドに設定した場合は指定したアクションのみが対象です。

#### disabled

trueに設定した場合、認証不要であることを表します。初期値はfalseなので、単に `Secure` アノテーションを設定した場合は認証が必要になります。

- type: boolean
- default: false

#### roles

必要な権限を配列で設定します。現在この値を用いた認可処理は未実装です。

- type: array
- default: []


### SecureConfig アノテーション

`SecureConfig` アノテーションは認証に関する設定を行います。適応可能範囲は `Secure` アノテーションと同様です。

#### domain

同一プロジェクト内で異なる認証処理を行わなければならない場合（ユーザ専用ページ、管理者専用ページなど）、認証状況が適応される領域を指定したい場合に指定します。

より技術的に説明すると、認証状態は基本的にセッションに格納され、domainはセッションの名前空間となります。

- type: boolean
- default: "default"

#### strategy

認証状態の管理方法を指定します。初期値は "session" で、セッションを用いた認証状態の管理を行います。

この他にも、FacebookのPHP-SDKの状態と連動させた "facebook" などが指定できます。

- type: boolean
- default: "session"

#### forward

非ログイン状態で認証が必要なコントローラにアクセスした場合、ここに指定したコントローラが呼び出されます。コントローラのメソッド名（クラス::メソッド）を指定するか、Symfonyの短縮形式（バンドル名:コントローラ名:アクション名）でも指定できます。forwardが指定されていない場合に認証が必要なコントローラにアクセスした場合はエラーになります。

forwardに指定したコントローラへのアクセスは、無限ループを防ぐため、認証が必須と設定されている場合であっても制御は行いません。

- type: string


### クラスに設定したアノテーション

`Secure` アノテーションがクラスに設定されている場合はすべてのメソッドに同じ内容が適応されます。親クラスに設定されている値も読み込まれます。親クラス -> 子クラス -> メソッド の順番に読み込まれ、あとに読み込まれた値で上書きされます。

disabled属性を指定しなかった場合は認証が必要として上書きされますが、その他の属性は指定しない限り上書きされません。メソッドのアノテーションが読み込まれた段階で指定されていない場合のみ、デフォルト値が設定されます。


### サンプルコード

次のコードはアノテーションを用いて認証を行うサンプルコードです。 `CrocosSecurityBundle` を使用する際は、設定をしやすくするためにアプリケーションごとに共通のコントローラクラスを作成することを推奨します。

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

管理者用のコントローラを作る場合、次のようにdomain属性を指定して、別の認証領域とします。この場合はデフォルトで認証が必要になります。

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

認証に関わる状態は `crocos_security.context` というキーでサービスコンテナに登録されている、 `Crocos\SecurityBundle\Security\SecurityContext` オブジェクトが保持しています。なお実際の処理内容については後述する `AuthStrategy` によって変更可能です。

### ログイン

ログインを行うには、`login()` メソッドにユーザ情報を渡します。

    $this->get('crocos_security.context')->login('Katsuhiro Ogawa');


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


AuthStrategy
--------------

`AuthStrategy` は認証状態の管理方法を切り替える仕組みです。`Secure` アノテーションの `strategy` と対応しています。標準では、セッションを用いて認証状態の管理を行う `SessionAuth` (strategy="session")、Facebook PHP SDKに状態管理を委譲する `FacebookAuth` (strategy="facebook") の2つがあります。また、既存の `AuthStrategy` を拡張したり、独自に作成することも可能です。

### SessionAuth

`SessionAuth` はセッションを用いて認証状態を管理する仕組みです。

### FacebookAuth

`FacebookAuth` はFacebook PHP SDKを用いて認証を行います。

`login()` および `logout()` メソッドは使用できません。`BaseFacebook::getLoginUrl()` を用いて認証してください。

`FacebookAuth` を利用する場合は、`facebook.api` というキーで `Facebook` オブジェクトをDIコンテナにサービス登録してください。
