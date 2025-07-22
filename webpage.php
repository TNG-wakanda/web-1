<?php
$conn = new mysqli("localhost", "root", "", "register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM site_settings WHERE id=1");
$settings = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($settings['site_name'] ?? 'TNG') ?></title>
  <style>
    body {
      font-family: 'Courier New', Courier, monospace;
      background-color: whitesmoke;
      color: black;
      margin: 0;
      padding: 0;
    }

    button.back-to-top {
      position: fixed;
      bottom: 30px;
      right: 30px;
      padding: 10px 15px;
      background-color: black;
      color: white;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-size: 16px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    header {
      background-color: white;
      color: black;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      position: sticky;
      top: 0;
      z-index: 999;
    }

    header h1 {
      margin: 0;
      display: flex;
      align-items: center;
    }

    header img {
      width: 50px;
      height: 30px;
      margin-right: 10px;
      border-radius: 15px;
    }

    nav ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      gap: 20px;
    }

    nav li {
      position: relative;
    }

    nav a {
      text-decoration: none;
      color: black;
      padding: 7px 15px;
      display: block;
      font-size: large;
      font-weight: bolder;
    }

    nav a:hover {
      color: blue;
    }

    .submenu {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      background: gainsboro;
      padding: 10px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .submenu a {
      color: black;
      margin: 5px 0;
    }

    nav li:hover .submenu {
      display: block;
    }

    .container {
      display: flex;
      flex-direction: row;
      width: 100%;
      height: 100%;
      padding: 20px;
    }

    .sect1 {
      width: 50%;
      height: 550px;
    }

    .sect2 {
      width: 50%;
      height: 550px;
    }

    .sect2 img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 10px;
    }

    .sect1 p {
      font-size: large;
      margin: 10px 0;
      padding: 10px;
    }

    .get_started {
      margin-left: 7%;
      margin-top: 100px;
      width: 190px;
      height: 50px;
      background-color: #3D63F1;
      border-radius: 50px;
      text-align: center;
      display: flex;
      box-shadow: 5px 5px 5px 5px lightblue;
    }

    .get_started a {
      color: white;
      margin: auto;
      text-decoration: none;
    }

    .house {
      display: flex;
      justify-content: space-around;
      text-align: center;
      width: 100%;
      height: 400px;
      background-color: white;
      padding: 20px;
    }

    .chamber {
      width: 30%;
      height: 100%;
    }

    .chamber h2 {
      font-size: 30px;
      text-align: center;
    }

    .chamber p {
      text-align: center;
      font-size: 20px;
    }

    .chamber img {
      width: 100%;
      height: 50%;
      object-fit: cover;
      margin-top: 10px;
      border-radius: 10px;
    }

    .chamber a {
      text-decoration: none;
      color: blue;
      font-size: 20px;
      display: block;
      margin-top: 10px;
    }

    .why {
      text-align: center;
      width: 100%;
      height: 300px;
      padding: 20px;
    }

    .why p {
      margin-top: 20px;
      font-size: 18px;
    }

    .why h2 {
      margin-bottom: 100px;
      font-size: 30px;
    }

    .pagr {
      background-color: #2C2B3D;
      width: 100%;
      height: auto;
      text-align: center;
      color: white;
      padding: 40px 20px;
    }

    .pagr h2 {
      font-size: 30px;
      margin-bottom: 20px;
    }

    .pagr p {
      font-size: 20px;
      margin: 10px auto;
      max-width: 900px;
    }

    .pagr a {
      text-decoration: none;
      color: white;
      font-size: 20px;
      display: inline-block;
      margin-top: 10px;
      padding: 10px 20px;
      background-color: #3D63F1;
      border-radius: 50px;
    }

    footer {
      text-align: center;
      padding: 20px;
      background: #2C2B3D;
      color: white;
    }

    footer a {
      color: white;
      text-decoration: underline;
      margin: 0 5px;
    }
  </style>
</head>
<body>
  <header>
    <h1><img src="img/logo2.jpg" alt="Logo"><?= htmlspecialchars($settings['site_name'] ?? "TNG") ?></h1>
    <nav>
      <ul>
        <li><a href="webpage.php">Home</a></li>
        <li>
          <a href="#">Products</a>
          <div class="submenu">
            <a href="product1.html">Product 1</a>
            <a href="product2.html">Product 2</a>
            <a href="product3.html">Product 3</a>
          </div>
        </li>
        <li><a href="contact.html">Contacts</a></li>
      </ul>
    </nav>
  </header>

  <div class="container">
    <div class="sect1">
      <h1>Welcome to <?= htmlspecialchars($settings['site_name'] ?? "TNG") ?></h1>
      <p><?= nl2br(htmlspecialchars($settings['description'] ?? "We are the best online selling company that works closely with our clients.")) ?></p>
      <div class="get_started">
        <a href="register.php">Get Started</a>
      </div>
    </div>
    <div class="sect2">
      <img src="img/pic inaction.jpg" alt="in meeting">
    </div>
  </div>

  <div class="why">
    <h2>Why <?= htmlspecialchars($settings['site_name'] ?? "TNG") ?>?</h2>
    <p>We are the best online selling company all over the world that works very closely with our customers or clients.</p>
  </div>

  <div class="house">
    <div class="chamber">
      <img src="img/pic coin.jpg" alt="sells">
      <h2><?= htmlspecialchars($settings['site_name'] ?? "TNG") ?> Sells</h2>
      <p>We sell all kinds of products online.</p>
      <a href="sells.html">read more</a>
    </div>
    <div class="chamber">
      <img src="img/pic car.jpg" alt="transport">
      <h2><?= htmlspecialchars($settings['site_name'] ?? "TNG") ?> Transport</h2>
      <p>After buying, no matter what, the product comes to find you.</p>
      <a href="transport.html">read more</a>
    </div>
    <div class="chamber">
      <img src="img/pic gain.jpg" alt="payment">
      <h2><?= htmlspecialchars($settings['site_name'] ?? "TNG") ?> Payment</h2>
      <p>We accept all kinds of methods across the world.</p>
      <a href="payment.html">read more</a>
    </div>
  </div>

  <div class="pagr">
    <h2>Buying is easy and accessing our web is totally free</h2>
    <p>Shopping with <?= htmlspecialchars($settings['site_name'] ?? "TNG") ?> is simple and convenient. Browse our products and order from home!</p>
    <p>Enjoy a 30-day FREE trial on select products.</p>
    <p>Our team will guide you every step of the way.</p>
    <p>We process and deliver orders right to your door.</p>
    <a href="chat.php">Contact Sales</a>
  </div>

  <footer>
    <p>Contact us: <?= htmlspecialchars($settings['contact_email']) ?> | Phone: <?= htmlspecialchars($settings['contact_phone']) ?> | Address: <?= htmlspecialchars($settings['contact_address']) ?></p>
    <p>
      Follow us:
      <a href="<?= htmlspecialchars($settings['facebook']) ?>" target="_blank">Facebook</a> |
      <a href="<?= htmlspecialchars($settings['twitter']) ?>" target="_blank">Twitter</a> |
      <a href="<?= htmlspecialchars($settings['instagram']) ?>" target="_blank">Instagram</a>
    </p>
  </footer>

  <button onclick="window.scrollTo({ top: 0, behavior: 'smooth' });" class="back-to-top">↑</button>
</body>
</html>
