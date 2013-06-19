<?php
$settings->add(new admin_setting_heading(            
	'headerconfig',            
	get_string('headerconfig', 'block_evasys'),            
	get_string('descconfig', 'block_evasys')    ,''    
)); 
$settings->add(new admin_setting_configtext(            
	'evasys/wsdl',            
	get_string('configlabel_wsdl', 'block_evasys'),            
	get_string('configdesc_wsdl', 'block_evasys'),''
));
$settings->add(new admin_setting_configtext(            
	'evasys/header',            
	get_string('configlabel_header', 'block_evasys'),            
	get_string('configdesc_header', 'block_evasys'),''
));
$settings->add(new admin_setting_configtext(            
	'evasys/soapuser',            
	get_string('configlabel_soapuser', 'block_evasys'),            
	get_string('configdesc_soapuser', 'block_evasys'),''
));
$settings->add(new admin_setting_configpasswordunmask(
	'evasys/soappass', 
	get_string('configlabel_soappass', 'block_evasys'),
	get_string('configdesc_soappass', 'block_evasys'), 
	'password'
));
$settings->add(new admin_setting_configtext(            
	'evasys/participation_url',            
	get_string('configlabel_participation_url', 'block_evasys'),            
	get_string('configdesc_participation_url', 'block_evasys'),''
));
$settings->add(new admin_setting_configtext(            
	'evasys/proxy',            
	get_string('configlabel_proxy', 'block_evasys'),            
	get_string('configdesc_proxy', 'block_evasys'),''
));
$settings->add(new admin_setting_configtext(            
	'evasys/proxyport',            
	get_string('configlabel_proxyport', 'block_evasys'),            
	get_string('configdesc_proxyport', 'block_evasys'),''
));