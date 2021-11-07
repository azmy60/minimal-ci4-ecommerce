<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<style>
		* {
			box-sizing: border-box;
		}

		html, body {
			padding: 0;
			margin: 0;
		}

		body {
			font-family: sans-serif;
			font-size: 18px;
			padding: 1rem;
			color: black;
			display: grid;
			place-content: center;
			height: 100vh;
		}

		a {
			color: black;
			font-weight: bold;
			text-decoration: none;
		}

		a:focus {
			outline: 0.2rem solid;
			outline-offset: 0.2rem;
			border-radius: 0.5rem;
		}

		a:focus:not(:focus-visible) {
			outline: none;
		}

		a:focus-visible {
			outline: 0.2rem solid;
			outline-offset: 0.2rem;
			border-radius: 0.5rem;
		}

		.btn {
			padding: 0.5rem 1rem;
			text-decoration: none;
			border: 1px solid;
			border-radius: 0.5rem;
			font-weight: normal;
		}
	</style>
</head>

<body>
	<main>
		<?php if (logged_in()) : ?>
			<span>Logged in as <?= user()->toArray()['email'] ?> <a class="btn" style="margin-left: 0.5rem;" href="/logout">logout</a></span>
		<?php else : ?>
			<span>Not logged in.</span>
			<br>
			<br>
			<div>
				<a href="/login">Login</a>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="/register">Create a new account</a>
			</div>
		<?php endif ?>
	</main>
</body>

</html>