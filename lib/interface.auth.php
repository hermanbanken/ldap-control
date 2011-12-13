<?php
interface iAuth {
	public function is_connected();

	public function get_users();	

	public function ask_auth();
	public function auth_user($uid, $pass);
	public function logout_user();
	public function is_authenticated();
}
?>