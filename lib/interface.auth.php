<?php
interface iAuth {
	private function connect();
	public function is_connected();

	public function get_users();	

	public function ask_auth();
	public function auth_user($uid, $pass);
	public function is_authenticated();
}
?>