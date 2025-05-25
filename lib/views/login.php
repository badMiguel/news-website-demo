<h1>Login</h1>

<form class="login-form" method="POST" action="/login">
    <label for="username">Username: </label>
    <input type="text" name="username" placeholder="Username" required>
    <label for="password">Password: </label>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
