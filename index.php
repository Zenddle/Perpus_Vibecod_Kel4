<?php

// 1. Load file konfigurasi
require_once 'config/config.php';

// 2. Load core classes secara manual agar dipastikan ada
require_once 'app/Core/Router.php';
require_once 'app/Core/Controller.php';
require_once 'app/Core/Database.php';

// 3. Load berkas pendaftaran route
require_once 'app/Routes/web.php';

// 4. Jalankan router
Route::run();

//astagfirullah