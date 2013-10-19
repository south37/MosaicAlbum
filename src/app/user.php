<?php
use Respect\Validation\Validator as v;

ログイン: {
    $app->get('/user/login', function() use ($app, $container) {
            $app->render('user/login.html.twig');
        })
        ->name('user_login')
    ;
    $app->post('/user/login', function() use ($app, $container) {
            $input = $app->request()->post();

            // 入力チェック
            // http://documentup.com/Respect/Validation/
            $inputValidator = v::arr()
                ->key('email', v::string()->setName('mailaddress')->notEmpty())
                ->key('password', v::string()->setName('password')->notEmpty())
                ;

            $errors = [];
            try {
                $inputValidator->assert($input);
            } catch (\InvalidArgumentException $e) {
                $errors = $e->findMessages([
                        'mailaddress.notEmpty' => 'メールアドレスを入力してください',
                        'password.notEmpty' => 'パスワードを入力してください',
                    ]);
            }

            if (count($errors) === 0) {
                // ユーザーの存在チェック
                $repository = $container['repository.user'];
                $user = $repository->findByEmailPassword($input['email'], $input['password']);
                if (!$user) {
                    $errors['form'] = 'メールアドレスまたはパスワードを確認してください';
                    $app->render('user/login.html.twig', ['errors' => $errors, 'input' => $input]);
                    $app->stop('メールアドレスまたはパスワードを確認してください');
                }
                $container['session']->set('isLogin', true);
                $container['session']->set('user.name', $user->name);
                $container['session']->set('user.id', $user->id);
                $app->redirect($app->urlFor('welcome'));
            }

            $app->render('user/login.html.twig', ['errors' => $errors, 'input' => $input]);

        })
        ->name('user_login_post')
    ;
}

ログアウト: {
    $app->get('/user/logout', function() use ($app, $container) {
            $container['session']->clear();
            $app->redirect($app->urlFor('top'));
        })
        ->name('user_logout')
    ;
}

新規登録: {
    $app->get('/user/register', function () use ($app, $container) {
            $app->render('user/register.html.twig');
        })
        ->name('user_register')
    ;
    $app->post('/user/register', function () use ($app, $container) {
            $input = $app->request()->post();

            // ここでは上記コントローラーと異なり
            // バリデータをコントローラーの内部ではなく外部に委譲した実装例
            $validator = new \Vg\Validator\UserRegister();

            if ($validator->validate($input)) {
                $user = new \Vg\Model\User();
                $user->setProperties($input);

                $repository = $container['repository.user'];
                try {
                    $repository->insert($user);
                } catch (Exception $e) {
                    $app->halt(500, $e->getMessage());
                }

                $app->redirect($app->urlFor('user_login'));
            }

            $app->render('user/register.html.twig', ['errors' => $validator->errors(), 'input' => $input]);
        })
        ->name('user_register_post')
    ;
}

編集: {

    /**
     * ログインしていない場合はログイン画面にリダイレクト
     *
     * http://docs.slimframework.com/#Route-Middleware
     *
     * @param $session
     *
     * @return callable
     */
    $rediretIfNotLogin = function ( $session ) {
        return function () use ( $session ) {
            if ( $session->get('isLogin') !== true ) {
                $app = \Slim\Slim::getInstance();
                $app->flash('error', 'Login required');
                $app->redirect($app->urlFor('user_login'));
            }
        };
    };

    $app->get('/user/edit', $rediretIfNotLogin($container['session']), function () use ($app, $container) {
            $repository = $container['repository.user'];
            $user = $repository->findById($container['session']->get('user.id'));

            $app->render('user/edit.html.twig', ['input' => $user]);
        })
        ->name('user_edit')
    ;

    $app->post('/user/update', function () use ($app, $container) {
            $input = $app->request()->post();

            $validator = new \Vg\Validator\UserEdit();

            if ($validator->validate($input)) {
                $repository = $container['repository.user'];
                $user = $repository->findById($container['session']->get('user.id'));
                $user->name = $input['name'];
                $user->email = $input['email'];
                $user->birthday = $input['birthday'];

                try {
                    $repository->update($user);
                } catch (Exception $e) {
                    $app->halt(500, $e->getMessage());
                }

                $container['session']->set('user.name', $user->name);
                $app->redirect($app->urlFor('welcome'));
            }

            $app->render('user/edit.html.twig', ['errors' => $validator->errors(), 'input' => $input]);
        })
        ->name('user_update')
    ;
}
