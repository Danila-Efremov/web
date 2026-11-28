<?php
declare(strict_types=1);

session_start();

// Проверка метода отправки
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Проверка не отключен ли показ картинок в браузере
    if (!isset($_SESSION['captcha_enabled'])) {
        $_SESSION['message'] = 'Пожалуйста, включите отображение изображений в вашем браузере.';
        $_SESSION['messageType'] = 'warning';
    } else {

        // Получение введенного пользователем ответа
        $userAnswer = $_POST['answer'] ?? '';

        // Удаление пробелов и приведение к верхнему регистру
        $userAnswer = strtoupper(trim($userAnswer));

        // Получение правильного ответа из сессии
        $correctAnswer = $_SESSION['captcha'] ?? '';

        // Проверка введенных данных
        if (empty($userAnswer)) {
            $_SESSION['message'] = 'Пожалуйста, введите символы с изображения!';
            $_SESSION['messageType'] = 'error';

        } elseif ($userAnswer === $correctAnswer) {
            $_SESSION['message'] = 'Отлично! Вы ввели правильную строку: ' . htmlspecialchars($correctAnswer);
            $_SESSION['messageType'] = 'success';

            // Очистка captcha после успешной проверки
            unset($_SESSION['captcha']);

        } else {
            $_SESSION['message'] = 'Неправильно! Вы ввели: "' . htmlspecialchars($userAnswer) .
                      '", а правильный ответ: "' . htmlspecialchars($correctAnswer) . '"';
            $_SESSION['messageType'] = 'error';
        }
    }

    // Редирект для предотвращения повторной отправки формы
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Получение сообщения из сессии и его очистка
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['messageType'] ?? '';

// Очистка сообщения из сессии после извлечения
if (!empty($message)) {
    unset($_SESSION['message']);
    unset($_SESSION['messageType']);
}
?>

<!DOCTYPE HTML>
<html>

<head>
  <meta charset="utf-8">
  <title>Регистрация с CAPTCHA</title>
  <link rel="stylesheet" href="css/reg-style.css">
</head>

<body>
  <noscript>
    <div class="container">
      <div class="message warning">
        <strong>У вас отключен JavaScript!</strong> Для корректной работы проверки картинки рекомендуется включить его в настройках браузера.
      </div>
    </div>
  </noscript>

  <!-- Основной контент (ВИДЕН по умолчанию) -->
  <div class="container" id="main-content">
    <h1>Регистрация</h1>

    <p>
      <a href="../index.html" class="back-link">На главную</a>
    </p>

    <div class="info">
      Введите символы, которые видите на изображении ниже (CAPTCHA).
    </div>
    
    <form method="post">
      <div class="captcha-box">
        <img src="noise-picture.php?<?php echo time(); ?>" id="captcha-image" alt="CAPTCHA">
        <div class="reload-link">
          <a href="javascript:location.reload()">Обновить картинку</a>
        </div>
      </div>
      
      <div class="form-group">
        <label for="answer">Введите символы с изображения:</label>
        <!-- Добавлен autofocus для мгновенного выделения -->
        <input type="text" name="answer" id="answer" size="6" maxlength="6" 
               placeholder="XXXXX" autocomplete="off" required autofocus>
      </div>
      
      <input type="submit" value="Подтвердить">
    </form>
    
    <?php if ($message): ?>
    <div class="message <?php echo $messageType; ?>">
      <?php echo $message; ?>
    </div>
    <?php endif; ?>
    
    <div class="info" style="margin-top: 30px;">
      <ul style="margin: 10px 0; padding-left: 20px;">
        <li>Регистр не имеет значения</li>
        <li>Только латинские буквы и цифры</li>
        <li>Символы 0/O, I/l исключены</li>
        <li>Если не видно - обновите страницу</li>
      </ul>
    </div>
  </div>

  <!-- Блок предупреждения (СКРЫТ по умолчанию) -->
  <div class="container" id="no-images-message" style="display:none;">
    <h1>Внимание</h1>
    <div class="message warning" style="font-size: 18px; padding: 20px;">
      <strong>Не удалось отобразить CAPTCHA.</strong><br><br>
      Пожалуйста, убедитесь, что в настройках вашего браузера <strong>включено отображение картинок</strong> для этого сайта, и обновите страницу. Если картинки включены, возможно, на сервере произошла ошибка при генерации капчи.
    </div>
  </div>

  <script>
  // Функция проверки картинки
  function checkCaptchaImage() {
      var img = document.getElementById('captcha-image');
      var warning = document.getElementById('no-images-message');
      var mainContent = document.getElementById('main-content');

      // Если картинка не загружена или её ширина равна 0 (битая картинка)
      if (!img.complete || img.naturalWidth === 0) {
          warning.style.display = 'block';
          mainContent.style.display = 'none';
      } else {
          warning.style.display = 'none';
          mainContent.style.display = 'block';
      }
  }

  // Проверяем после полной загрузки страницы
  window.addEventListener('load', checkCaptchaImage);

  // Страховка: проверяем еще раз через 2 секунды (вдруг картинка грузится слишком долго или упала по таймауту)
  setTimeout(checkCaptchaImage, 2000);
  </script>
</body>

</html>
