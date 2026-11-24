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
  <div class="container">
    <h1>Регистрация</h1>

    <p>
      <a href="../index.html" class="back-link">На главную</a>
    </p>

    <div class="info">
      Введите символы, которые видите на изображении ниже (CAPTCHA).
    </div>

    <form method="post">
      <div id="captcha-section">
        <div class="captcha-box">
          <img src="noise-picture.php?<?php echo time(); ?>" alt="CAPTCHA">
          <div class="reload-link">
            <a href="javascript:location.reload()">Обновить картинку</a>
          </div>
        </div>

        <div class="form-group">
          <label for="answer">Введите символы с изображения:</label>
          <input type="text" name="answer" id="answer" size="6" maxlength="6"
                 placeholder="XXXXX" autocomplete="off" required>
        </div>

        <input type="submit" value="Подтвердить">
      </div>
    </form>

    <div id="no-images-message" class="message warning" style="display:none;">
      Похоже, что отображение изображений в вашем браузере отключено.
      Пожалуйста, включите отображение изображений, чтобы увидеть CAPTCHA.
    </div>

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

  <script>
  (function () {
      var testImg = new Image();

      testImg.onload = function () {
          // Изображения отображаются – показываем CAPTCHA и ставим фокус
          document.getElementById('captcha-section').style.display = 'block';
          document.getElementById('no-images-message').style.display = 'none';
          var input = document.getElementById('answer');
          if (input) {
              input.focus();
          }
      };

      testImg.onerror = function () {
          // Изображения отключены – скрываем форму CAPTCHA и показываем предупреждение
          document.getElementById('captcha-section').style.display = 'none';
          document.getElementById('no-images-message').style.display = 'block';
      };

      // 1x1 прозрачный GIF в виде data URI
      testImg.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
  })();
  </script>
</body>

</html>
