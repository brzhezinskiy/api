<?php
require_once ("core/auth.php");

if (empty($_POST) && empty($_GET)) {
	require_once ("assets/form.php");
}
else {
	require_once ("core/index.php");
}