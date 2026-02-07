<?php
session_start();

function require_siswa() {
  if (empty($_SESSION['siswa_nis'])) { header("Location: index.php"); exit; }
}
function require_admin() {
  if (empty($_SESSION['admin_id'])) { header("Location: index.php"); exit; }
}
