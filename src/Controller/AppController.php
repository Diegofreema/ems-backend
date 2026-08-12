<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ServiceUnavailableException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Legacy gateways update mutable invoices and transactions outside the EMS
     * finance ledger. Keep every entry and callback closed until replacement.
     *
     * @var array<string, array<int, string>>
     */
    private const LEGACY_ONLINE_PAYMENT_ACTIONS = [
        'Invoices' => ['verifyetransact', 'gettransactiondata', 'getpinvendingpayment'],
        'Transactions' => [
            'gotopaystack', 'paymentverificationstack', 'verifycredo', 'paymentverification',
            'remita_transaction_details', 'confirmremitapay', 'reconfirmtrxcredo',
            'cunonlineconfirmpay', 'transvalidate', 'getpaymentnotification',
            'getwebpayinterswitch', 'updatetx', 'retrypayment', 'getpaymentstatusfluter',
            'requeryfailedpayment', 'gotocredo',
        ],
        'Students' => [
            'generatepayeeid', 'getmypayeeid', 'gotopaystack', 'paymentverification',
            'verifypay', 'initiateremitapostjson', 'getsplitinvoice', 'remitasplit',
            'getictrrr', 'generateapplicantpayeeid', 'generatetranscriptpayeeid',
            'gettranscriptinvoice', 'getinvoiceforfees', 'getapplicantpayeeid',
            'paymentinvoice', 'gotopaystackotherfees', 'paymentverificationotherfees',
            'payfees', 'interswitchwebpay', 'gotocredo', 'verifycredo', 'getpayonline',
            'gotopaystacktest', 'paymentverificationtest',
        ],
        'Sparents' => ['gotopaystacktest', 'paymentverificationtest'],
        'SparentsStudents' => ['gotopaystacktest', 'paymentverificationtest'],
    ];

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        if (self::isLegacyOnlinePaymentAction(
            (string)$this->request->getParam('controller'),
            (string)$this->request->getParam('action')
        )) {
            throw new ServiceUnavailableException(
                'Legacy online payments are temporarily unavailable while the secure finance system is completed.'
            );
        }
    }

    public static function isLegacyOnlinePaymentAction(string $controller, string $action): bool
    {
        return in_array($action, self::LEGACY_ONLINE_PAYMENT_ACTIONS[$controller] ?? [], true);
    }

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

//       $this->loadComponent('RequestHandler', [
//            'enableBeforeRedirect' => false,
//        ]);
         $this->loadComponent('RequestHandler');
         $this->loadComponent('Flash');
        $this->loadComponent('Auth', [ //'authorize' => ['Controller'],
      'loginRedirect' => [ 'controller' =>'Admins', 'action' =>'dashboard'],
      'logoutRedirect' => [ 'controller' =>'Students', 'action' =>'index' ]]);

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //get base url
        $baseUrl = Router::url('/', true);
        $this->set('baseUrl', $baseUrl);
        $this->loadComponent('FormProtection');
        $this->viewBuilder()->addHelpers(['Html']);
         //get the logo on the login page
         $settings_Table = TableRegistry::get('Settings');
          $settings = $settings_Table->get(1,['contain'=>['Sessions','Semesters']]);
           $this->request->getSession()->write('settings', $settings);
    }
}
