<?php

  $url = env('APP_URL').'/api/cron/fitness-profile';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_ENCODING, ''); // this will handle gzip content
  $result = curl_exec($ch);
  curl_close($ch);
  echo $result;
