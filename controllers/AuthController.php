<?php
class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    private function verifyCsrf(): void
    {
        if (
            empty($_POST['csrf_token']) ||
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            $_SESSION['error'] = '不正なリクエストです。';
            $this->redirect('/login');
        }
    }

    public function showLoginForm(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        $t = $this->loadTranslations();

        $this->view('auth/login', [
            'title'        => $t['login'] ?? 'Login',
            'translations' => $t,
            'csrfToken'    => $this->csrfToken(),
        ]);
    }

    public function showRegisterForm(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        $t = $this->loadTranslations();

        $this->view('auth/register', [
            'title'        => $t['register'] ?? 'Register',
            'translations' => $t,
            'csrfToken'    => $this->csrfToken(),
        ]);
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        //CSRF 検証
        $this->verifyCsrf();

        $t = $this->loadTranslations();
        $data = [
            'email'    => $_POST['email']    ?? '',
            'password' => $_POST['password'] ?? '',
        ];
        $errors = $this->validate($data, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['old']   = $_POST;
            $this->redirect('/login');
        }

        $user = $this->userModel->findByEmail($data['email']);
        if ($user && $this->userModel->verifyPassword($data['password'], $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $this->redirect('/');
        }

        $_SESSION['error'] = $t['login_error'] ?? 'ログインに失敗しました。';
        $this->redirect('/login');
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }
        //CSRF 検証
        $this->verifyCsrf();

        $t = $this->loadTranslations();
        $data = [
            'name'     => $_POST['name']     ?? '',
            'email'    => $_POST['email']    ?? '',
            'password' => $_POST['password'] ?? '',
        ];

        $errors = $this->validate($data, [
            'name'     => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($data['password'] !== ($_POST['password_confirmation'] ?? '')) {
            $errors['password_confirmation'] = $t['password_mismatch'] ?? 'パスワードが一致しません。';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['old']   = $_POST;
            $this->redirect('/register');
        }

        if ($this->userModel->findByEmail($data['email'])) {
            $_SESSION['error'] = $t['email_taken'] ?? 'このメールアドレスは既に登録されています。';
            $_SESSION['old']   = $_POST;
            $this->redirect('/register');
        }

        if ($this->userModel->createUser($data)) {
            $_SESSION['success'] = $t['register_success'] ?? '登録が完了しました。ログインしてください。';
            $this->redirect('/login');
        }

        $_SESSION['error'] = $t['register_fail'] ?? '登録に失敗しました。もう一度お試しください。';
        $_SESSION['old']   = $_POST;
        $this->redirect('/register');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }
}
