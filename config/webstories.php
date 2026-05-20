<?php

return [
  'publisher_name' => env('STORY_PUBLISHER_NAME', 'Tio Ben IA'),
  'publisher_logo' => env('STORY_PUBLISHER_LOGO', env('APP_URL').'/images/logo-amp.webp'),

  'terco_poster' => env('STORY_TERCO_POSTER', env('APP_URL').'/images/stories/terco-default.jpg'),
  'terco_bg_dark' => env('STORY_TERCO_BG_DARK', env('APP_URL').'/images/stories/liturgia-bg-dark.jpg'),
  'terco_bg_light' => env('STORY_TERCO_BG_LIGHT', env('APP_URL').'/images/stories/liturgia-bg-light.jpg'),
];
