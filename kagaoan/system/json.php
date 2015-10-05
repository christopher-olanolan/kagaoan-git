<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$jsonmodule = '[
	{"module":"Control Panel"},
	{"module":"Dashboard"},
	{"module":"User",
		"sub-module": [
			{"group":"User",
			 "pages": [
				{"page": "Profile"},
				{"page": "Add User"},
				{"page": "User Management"}
			]},
			{"group":"User Group",
			 "pages": [
				{"page": "Add User Group"},
				{"page": "User Group Management"}
			]}
		]	
	}
]';
?>