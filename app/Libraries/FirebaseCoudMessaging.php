<<<<<<< HEAD
<?php

namespace App\Libraries;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;
use Kreait\Firebase\Messaging\RawMessageFromArray;

class FirebaseCoudMessaging
{
  function send($to, $title, $body, $data = [])
  {
    $raw = [
      'notification' => [
        'title' => $title,
        'body' => $body,
      ],
    ];
    if (count($data) > 1) $raw += ['data' => $data];

    $factory = (new Factory)->withServiceAccount(env('fcm.firebase_config_file_dir'));
    $messaging = $factory->createMessaging();
    $message = new RawMessageFromArray($raw);

    $report = $messaging->sendMulticast($message, $to);
    helper('rest_api');
    if ($report->hasFailures()) {
      $response = initResponse('Failed');
      $temp = "";
      foreach ($report->failures()->getItems() as $failure) {
        $temp .= $failure->error()->getMessage() . PHP_EOL;
      }
      $response->data['error'] = $temp;
      $response->data['failed_count'] = $report->failures()->count();
    } else {
      $response = initResponse('Success', true, ['success_count' => $report->successes()->count()]);
    }

    return $response;
  }
  function sendWebPush($to, $title, $body, $data = [], $icon = '', $link = '')
  {
    if (empty($icon)) $icon = base_url() . '/assets/images/logo.png';
    if (empty($link)) $icon = base_url();
    $raw = [
      'notification' => [
        'title' => $title,
        'body' => $body,
      ],
      'webpush' => [
        'headers' => [
          'Urgency' => 'normal',
        ],
        'notification' => [
          'title' => $title,
          'body' => $body,
          'icon' => $icon
        ],
        "fcm_options" => [
          "link" => $link
        ],
      ],
      // 'android' => $android,
      // 'apns' => $apns,
    ];
    if (count($data) > 1) $raw += ['data' => $data];

    $factory = (new Factory)->withServiceAccount(env('fcm.firebase_config_file_dir'));
    $messaging = $factory->createMessaging();
    $message = new RawMessageFromArray($raw);

    $report = $messaging->sendMulticast($message, $to);
    helper('rest_api');
    if ($report->hasFailures()) {
      $response = initResponse('Failed');
      $temp = "";
      foreach ($report->failures()->getItems() as $failure) {
        $temp .= $failure->error()->getMessage() . PHP_EOL;
      }
      $response->data['error'] = $temp;
      $response->data['failed_count'] = $report->failures()->count();
    } else {
      $response = initResponse('Success', true, ['success_count' => $report->successes()->count()]);
    }

    return $response;
  }

}
=======
<?php

namespace App\Libraries;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;
use Kreait\Firebase\Messaging\RawMessageFromArray;

class FirebaseCoudMessaging
{
  function send($to, $title, $body, $data = [])
  {
    $raw = [
      'notification' => [
        'title' => $title,
        'body' => $body,
      ],
    ];
    if (count($data) > 1) $raw += ['data' => $data];

    $factory = (new Factory)->withServiceAccount(env('fcm.firebase_config_file_dir'));
    $messaging = $factory->createMessaging();
    $message = new RawMessageFromArray($raw);

    $report = $messaging->sendMulticast($message, $to);
    helper('rest_api');
    if ($report->hasFailures()) {
      $response = initResponse('Failed');
      $temp = "";
      foreach ($report->failures()->getItems() as $failure) {
        $temp .= $failure->error()->getMessage() . PHP_EOL;
      }
      $response->data['error'] = $temp;
      $response->data['failed_count'] = $report->failures()->count();
    } else {
      $response = initResponse('Success', true, ['success_count' => $report->successes()->count()]);
    }

    return $response;
  }
  function sendWebPush($to, $title, $body, $data = [], $icon = '', $link = '')
  {
    if (empty($icon)) $icon = base_url() . '/assets/images/logo.png';
    if (empty($link)) $icon = base_url();
    $raw = [
      'notification' => [
        'title' => $title,
        'body' => $body,
      ],
      'webpush' => [
        'headers' => [
          'Urgency' => 'normal',
        ],
        'notification' => [
          'title' => $title,
          'body' => $body,
          'icon' => $icon
        ],
        "fcm_options" => [
          "link" => $link
        ],
      ],
      // 'android' => $android,
      // 'apns' => $apns,
    ];
    if (count($data) > 1) $raw += ['data' => $data];

    $factory = (new Factory)->withServiceAccount(env('fcm.firebase_config_file_dir'));
    $messaging = $factory->createMessaging();
    $message = new RawMessageFromArray($raw);

    $report = $messaging->sendMulticast($message, $to);
    helper('rest_api');
    if ($report->hasFailures()) {
      $response = initResponse('Failed');
      $temp = "";
      foreach ($report->failures()->getItems() as $failure) {
        $temp .= $failure->error()->getMessage() . PHP_EOL;
      }
      $response->data['error'] = $temp;
      $response->data['failed_count'] = $report->failures()->count();
    } else {
      $response = initResponse('Success', true, ['success_count' => $report->successes()->count()]);
    }

    return $response;
  }

}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
