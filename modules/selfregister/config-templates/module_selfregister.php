<?php
/**
 * The configuration of selfregistration module
 */

$config = array (


	// Storage backend SQL
	'storage.backend' => 'SQL',

	// The authentication source that should be used.
	// Keep in mind that selfregister need permissions to write.
	//'auth' => 'selfreg-pg',
	'auth' => 'selfregister-mysql',

	//Used in mail and on pages
	'system.name' => 'SimpleSAMLphp guest IdP',

	// Mailtoken validity
	// FIXME this is still hardcoded
	'mailtoken.lifetime' => (3600*24*5),

	// FIXME make this default to technicalcontact_name etc.
	'mail.from'     => 'Selfregister admin <admin@example.org>',
	'mail.replyto'  => 'Selfregister support <support@example.org>',
	'mail.subject'  => 'E-mail verification',
	'mail.tracker' => 'Selfregister admin <tracker@example.org>',

	// A PHP hashing algorithm that is also supported by your database.
	// The SHA2 family is good choice. Carefully construct the SQL in 
	// your authsource to authenticate.
	'hash.algo'	=> 'sha512',

	// SQL write backend configuration
	'sql' => array(
		// User ID field in the database.
		// This is usually the primary key
		// This relates to the attributs mapping (see below)
		'user.id.param' => 'userid',
	), // end SQL config


	// Mapping from the Storage backend field names to web frontend field names
	// This also indicate which user attributes that will be saved


	'attributes'  => array(
		'username'	=> 'uid',
		'firstname'	=> 'givenName',
		'lastname'	=> 'sn',
		'email'		=> 'mail',
		'userPassword'	=> 'password',
	),



	// Configuration for the field in the web frontend
	// This controlls the order of the fields
	'formFields' => array(

		'givenName' => array(
			'validate' => FILTER_DEFAULT,
			'layout' => array(
				'control_type' => 'text',
				'show' => true,
				'read_only' => false,
			),
		),

		'sn' => array(
			'validate' => FILTER_DEFAULT,
			'layout' => array(
				'control_type' => 'text',
				'show' => true,
				'read_only' => false,
			),
		),
		'mail' => array(
			'validate' => FILTER_VALIDATE_EMAIL,
			'layout' => array(
				'control_type' => 'text',
				'show' => 1,
				'read_only' => 0,
			),
		),

		'userPassword' => array(
			'validate' => FILTER_DEFAULT,
			'layout' => array(
				'control_type' => 'password',
			),
		),
		'pw1' => array(
			'validate' => FILTER_DEFAULT,
			'layout' => array(
				'control_type' => 'password',
			),
		),
		'pw2' => array(
			'validate' => FILTER_DEFAULT,
			'layout' => array(
				'control_type' => 'password',
			),
		),
	)
);