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

//bisa kah zak
<<<<<<< HEAD
//ada kah zik
//apalagi bre
=======
//ada kah zik
>>>>>>> ed06276420b74262ff6e5e3f28220180498164ca
