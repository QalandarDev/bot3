<?php

namespace app\controllers;

use aki\telegram\types\Poll;
use app\models\Buttons;
use app\models\Polls;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @void
     */

    const ADMIN_ID = 354742944;
    public $enableCsrfValidation = false;

    public function actionIndex(): void
    {

        $telegram = Yii::$app->telegram;
        $message = @$telegram->input->message;
        if ($message) {
            if ($message->chat->id != self::ADMIN_ID) {
                exit();
            }
            $checkAdmin = \app\models\Admin::findOne($message->chat->id);
            if (!$checkAdmin) {
                $admin = new \app\models\Admin();
                $admin->user_id = $message->chat->id;
                $admin->save();
            }
            $admin = \app\models\Admin::findOne($message->chat->id);
            if ($admin->step == 3 and $message->text == "/done") {
                $keyboardText = json_decode($admin->keyboard, true);
                $vote = new \app\models\Vote();
                $vote->name = $admin->text;
                $vote->save();

                $inline = [];
                foreach ($keyboardText as $btn) {
                    $voteItem = new Buttons();
                    $voteItem->vote_id = $vote->id;
                    $voteItem->text = $btn;
                    $voteItem->save();
                    $keyboard = [];
                    $keyboard[] = ['text' => $btn, 'callback_data' => json_encode(['vote' => $vote->id, 'button' => $voteItem->id])];
                    $inline[] = $keyboard;
                }
                $telegram->sendMessage([
                    'chat_id' => $message->chat->id,
                    'text' => "Sizning vote tayyor",
                    'reply_markup' => json_encode([
                        'inline_keyboard' =>
                            $inline
                    ]),
                ]);
                $admin->step = 0;
                $admin->text = '';
                $admin->keyboard = '';
                $admin->save();
            }
            if ($admin->step == 2) {
                $keyboardTexts = explode(',', $message->text);

                $inline = [];
                foreach ($keyboardTexts as $keyboardText) {
                    $keyboard = [];
                    $keyboard[] = ['text' => $keyboardText, 'callback_data' => time()];
                    $inline[] = $keyboard;
                }
                $admin->step = 3;
                $admin->keyboard = json_encode($keyboardTexts);
                $admin->save();
                $telegram->sendMessage([
                    'chat_id' => $message->chat->id,
                    'text' => $admin->text . "\n" . "Klavatura tayyor\n Tasdiqlash /done\nBekor qilish /start",
                    'reply_markup' => json_encode([
                        'inline_keyboard' =>
                            $inline
                    ]),
                ]);
            }
            if ($admin->step == 1) {
                $admin->text = $message->text;
                $admin->step = 2;
                $admin->save();
                $telegram->sendMessage([
                    'chat_id' => $message->chat->id,
                    'text' => "Klavaturani kiriting"
                ]);
            }
            if ($message->text == "/start" and $admin->step == 0) {
                $telegram->sendMessage([
                    'chat_id' => $message->chat->id,
                    'text' => "Matn kiriting",
                ]);
                $admin->step = 1;
                $admin->save();
            }

        }
        $query = @$telegram->input->callback_query;
        if ($query) {
            $data = json_decode($query->data, true);
            if (Polls::find()->where([
                'vote_id' => $data['vote'],
                'user_id' => $query->message['chat']['id']
            ])->exists()) {
                $telegram->answerCallbackQuery([
                    'callback_query_id' => $query->id,
                    'text' => "Siz oldinroq ovoz bergansiz",
                    'show_alert' => true
                ]);

            } else {
                $poll = new Polls();
                $poll->vote_id = $data['vote'];
                $poll->button_id = $data['button'];
                $poll->user_id = $query->message['chat']['id'];
                $poll->save();
                //add vote count to before button tex   t
                $buttons = Buttons::find()->where(['vote_id' => $data['vote']])->all();
                $inline = [];
                /** @var $button Buttons */
                foreach ($buttons as $button) {
                    $keyboard = [];
                    $count = Polls::find()->where(['vote_id' => $data['vote'], 'button_id' => $button->id])->count();
                    $keyboard[] = ['text' => $button->text . " [" . $count . ']', 'callback_data' => json_encode(['vote' => $button->vote_id, 'button' => $button->id])];
                    $inline[] = $keyboard;
                }
                $telegram->editMessageText([
                    'chat_id' => $query->message['chat']['id'],
                    'message_id' => $query->message['message_id'],
                    'text' => "Sizning ovozingiz qabul qilindi" . time(),
                    'reply_markup' => json_encode([
                        'inline_keyboard' =>
                            $inline
                    ]),
                ]);
            }
        }

    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public
    function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public
    function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public
    function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public
    function actionAbout()
    {
        return $this->render('about');
    }
}
