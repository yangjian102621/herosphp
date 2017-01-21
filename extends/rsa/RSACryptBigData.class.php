<?php
namespace rsa;



/**
 * RSA大长度数据加解密类，把内容分段加解密，解决RSA加解密长度限制
 */
class RSACryptBigData
{
	//公钥加密
	public function encryptByPublicKey_data($data, $publickey = '')
	{
		$RSACrypt = new RSACrypt();

		if ($publickey != "") {
			$RSACrypt->pubkey = $publickey;
		}
		$crypt_res = "";
		for ($i = 0; $i < ((strlen($data) - strlen($data) % 117) / 117 + 1); $i++) {
			$crypt_res = $crypt_res . ($RSACrypt->encryptByPublicKey(mb_strcut($data, $i * 117, 117, 'utf-8')));
		}
		return $crypt_res;
	}

	//私钥解密
	public function decryptByPrivateKey_data($data, $privatekey = '')
	{
		$RSACrypt = new RSACrypt();

		if ($privatekey != "") {  // if null use default
			$RSACrypt->privkey = $privatekey;
		}
		$decrypt_res = "";
		$datas = explode('@', $data);
		foreach ($datas as $value) {
			$decrypt_res = $decrypt_res . $RSACrypt->decryptByPrivateKey($value);
		}
		return $decrypt_res;
	}

	//私钥加密
	public function encryptByPrivateKey_data($data, $privatekey = '')
	{
		$RSACrypt = new RSACrypt();

		if ($privatekey != "") {
			$RSACrypt->privkey = $privatekey;
		}
		$crypt_res = "";
		for ($i = 0; $i < ((strlen($data) - strlen($data) % 117) / 117 + 1); $i++) {
			$crypt_res = $crypt_res . ($RSACrypt->encryptByPrivateKey(mb_strcut($data, $i * 117, 117, 'utf-8')));
		}
		return $crypt_res;
	}

	//公钥解密
	public function decryptByPublicKey_data($data, $publickey = '')
	{
		$RSACrypt = new RSACrypt();

		if ($publickey != "") {
			$RSACrypt->pubkey = $publickey;
		}
		$decrypt_res = "";
		$datas = explode('@', $data);
		foreach ($datas as $value) {
			$decrypt_res = $decrypt_res . $RSACrypt->decryptByPublicKey($value);
		}
		return $decrypt_res;
	}


}
