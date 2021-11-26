<?php


#!# Heading ordering links broken on /machines/decommissioned.html
#!# IP address list selection should be manually assigned, and will show (in use)
#!# Add software module
#!# Make "End user ID" clickable in sinenomine integration


# Class to create a computing inventory administration system
# Version 1.0.0

# Licence: GPL
# (c) Martin Lucas-Smith, University of Cambridge


require_once ('frontControllerApplication.php');
class computingInventory extends frontControllerApplication
{
	# Function to assign defaults additional to the general application defaults
	public function defaults ()
	{
		# Specify available arguments as defaults or as NULL (to represent a required argument)
		$defaults = array (
			'database'				=> 'computinginventory',
			'table'					=> 'machines',
			'div'					=> 'computinginventory',
			'authentication'		=> true,
			'administrators'		=> true,
			#!# frontControllerApplication needs an 'administrator' option that effectively sets administrator=true on every action
			'description'			=> 'Computing inventory',
			'databaseStrictWhere'	=> true,
			'expandableCharacter'	=> "\n",
			'tabUlClass'			=> 'tabsflat',
			'useSettings'			=> true,
		);
		
		# Return the defaults
		return $defaults;
	}
	
	
	# Function to assign additional actions
	public function actions ()
	{
		# Specify additional actions
		$actions = array (
			'home' => array (
				'description' => false,
				'url' => '',
				'tab' => 'Home',
				'icon' => 'house',
				'administrator'	=> true,
			),
			'machines' => array (
				'description' => false,
				'url' => 'machines/',
				'tab' => 'Machines',
				'icon' => 'computer',
				'administrator'	=> true,
			),
			'search' => array (
				'description' => 'Advanced search',
				'url' => 'search/',
				'tab' => 'Advanced search',
				'icon' => 'magnifier',
				'administrator'	=> true,
			),
			'searchExport' => array (
				'url' => 'search/results.csv',
				'export' => true,
				'administrator'	=> true,
			),
			'attributes' => array (
				'description' => false,
				'url' => 'attributes/',
				'tab' => 'Index',
				'icon' => 'application_view_list',
				'administrator'	=> true,
			),
			'addmachine' => array (
				'description' => 'Add a new machine',
				'url' => 'machines/add.html',
				'tab' => 'Add machine',
				'icon' => 'add',
				'administrator'	=> true,
			),
			'decommissioned' => array (
				'description' => 'Decommissioned machines',
				'url' => 'machines/decommissioned.html',
				'usetab' => 'machines',
				'icon' => 'bin',
				'administrator'	=> true,
			),
			'machinetemplates' => array (	// NB 'templates' is an internal name so cannot be used
				'description' => 'Manage machine templates',
				'url' => 'templates/',
				'tab' => 'Machine templates',
				'icon' => 'application_double',
				'administrator'	=> true,
			),
			'templateadd' => array (
				'description' => 'Add a machine template',
				'url' => 'templates/',
				'usetab' => 'templates',
				'administrator'	=> true,
			),
			'templateedit' => array (
				'description' => 'View/edit a machine template',
				'url' => 'templates/',
				'usetab' => 'templates',
				'administrator'	=> true,
			),
			'ipaddresses' => array (
				'description' => false,
				'url' => 'ipaddresses/',
				'tab' => 'IPs',
				'icon' => 'world',
				'administrator'	=> true,
			),
			'database' => array (
				'description' => 'Edit data/lookups',
				'usetab' => 'admin',
				'url' => 'data/',
				'authentication' => true,
				'administrator'	=> true,
			),
			'data' => array (	// Used for e.g. AJAX calls, etc.
				'description' => 'Data point',
				'url' => 'data.html',
				'export' => true,
				'administrator'	=> true,
			),
			'import' => array (
				'description' => 'Initial data import',
				'url' => 'import/',
				'parent' => 'admin',
				'subtab' => 'Initial data import',
				'administrator'	=> true,
			),
			'locations' => array (
				'description' => 'Locations',
				'url' => 'locations/',
				'parent' => 'admin',
				'subtab' => 'Locations',
				'administrator'	=> true,
			),
			'types' => array (
				'description' => 'Machine types',
				'url' => 'types/',
				'parent' => 'admin',
				'subtab' => 'Machine types',
				'administrator'	=> true,
			),
			'refreshdns' => array (
				'description' => 'Refresh DNS lookups',
				'parent' => 'admin',
				'subtab' => 'Refresh DNS lookups',
				'administrator'	=> true,
			),
		);
		
		# Return the actions
		return $actions;
	}
	
	
	# Database structure definition
	public function databaseStructure ()
	{
		return "
			CREATE TABLE IF NOT EXISTS `administrators` (
			  `username__JOIN__people__people__reserved` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Username' PRIMARY KEY,
			  `active` enum('','Yes','No') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Yes' COMMENT 'Currently active?',
			  `editingStateMachines` text COLLATE utf8mb4_unicode_ci COMMENT 'Fields to display',
			  `editingStateIpaddresses` text COLLATE utf8mb4_unicode_ci COMMENT 'Fields to display'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Helpdesk administrators';
			
			CREATE TABLE IF NOT EXISTS `settings` (
			  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Automatic key (ignored)' PRIMARY KEY,
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Settings';
			
			CREATE TABLE IF NOT EXISTS `ipaddresses` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Automatic key' PRIMARY KEY,
			  `ipAddress` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'IP address' UNIQUE KEY,
			  `reserved` enum('','Yes','No') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'No' COMMENT 'Whether the IP address is reserved'
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='IP addresses';
			
			CREATE TABLE IF NOT EXISTS `locations` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Automatic key' PRIMARY KEY,
			  `building` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Building name',
			  `floor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Floor'
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table of locations (buildings/floors)';
			
			CREATE TABLE IF NOT EXISTS `machines` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID #' PRIMARY KEY,
			  `ipaddress` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP address' UNIQUE KEY,
			  `dnsName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'DNS name (looked-up automatically)',
			  `typeId` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type of machine',
			  `manufacturer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Manufacturer',
			  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Model',
			  `monitor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Monitor',
			  `processor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Processor',
			  `memory` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Memory',
			  `harddisk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hard disk',
			  `videocard` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Video card',
			  `networkcard` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Network card',
			  `locationId` int(11) DEFAULT NULL COMMENT 'Location',
			  `room` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Room',
			  `user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'End user ID',
			  `os` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Operating system',
			  `sitevariable` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Site variable',
			  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'OS image version',
			  `officeVersion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Office version',
			  `adobeSoftware` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Adobe software',
			  `serialnumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Serial number',
			  `macaddress` varchar(17) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'MAC address',
			  `tag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tag',
			  `owner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Owner',
			  `commissionedDate` date DEFAULT NULL COMMENT 'Commissioned date',
			  `decommissionedDate` date DEFAULT NULL COMMENT 'Decomissioned date',
			  `decommisionedTo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Decommisioned to',
			  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Notes'
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table of machines';
			
			CREATE TABLE IF NOT EXISTS `templates` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Automatic key' PRIMARY KEY,
			  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name for this profile',
			  `attribute` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Attribute',
			  `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Value'
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table of machine template attributes';
			
			CREATE TABLE IF NOT EXISTS `types` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Automatic key' PRIMARY KEY,
			  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Machine type'
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Machine types';
			
			INSERT INTO `types` (`type`) VALUES
				('Desktop'),
				('Laptop'),
				('Tablet'),
				('Printer'),
				('MFD'),
				('Monitor'),
				('Virtual machine'),
				('IP only'),
				('Single-board computer'),
				('KVM'),
				('Network'),
				('Projector'),
				('Server'),
				('UPS'),
				('Webcam'),
				('Wireless access point'),
				('Other')
			;
		";
	}
	
	
	# Additional initialisation
	protected function main ()
	{
		# Define SQL extracts relating to decommissioned machines
		$this->excludeDecommissionedSql = 'decommissionedDate IS NULL';
		$this->includeDecommissionedSql = 'decommissionedDate IS NOT NULL';
		
	}
	
	
	# Welcome screen
	public function home ()
	{
		# Start the page
		$html  = "\n\n" . "<p>Welcome to the online computing inventory.</p>";
		
		# Machines
		$html .= "\n<div class=\"graybox\">";
		$html .= "\n<h2>Machines</h2>";
		$html .= "\n<p><a href=\"{$this->baseUrl}/machines/\">Browse the machines</a> or search:</p>";
		$html .= "\n" . '<form method="get" action="' . $this->baseUrl . '/machines/search.html" class="search" name="search">
			<img src="/images/icons/magnifier.png" alt="" class="icon">
			<input name="q" type="text" size="45" value="" placeholder="Search machines" autofocus="autofocus" />&nbsp;<input value="Search!" accesskey="s" type="submit" class="button" />
		</form>';
		$html .= "\n</div>";
		
		# IP addresses
		$html .= "\n<div class=\"graybox\">";
		$html .= "\n<h2>IP addresses</h2>";
		$html .= "\n<p><a href=\"{$this->baseUrl}/ipaddresses/\">Browse the IP addresses</a> or search for a machine using one:</p>";
		$this->ipAddressSearchBox ($html);
/*
		$html .= "\n" . '<form method="get" action="' . $this->baseUrl . '/ipaddresses/search.html" class="search" name="search">
			<img src="/images/icons/magnifier.png" alt="" class="icon">
			<input name="q" type="text" size="30" value="" placeholder="Search IP addresses" />&nbsp;<input value="Search!" accesskey="s" type="submit" class="button" />
		</form>';
*/
		$html .= "\n</div>";
		
		# Show the HTML
		echo $html;
	}
	
	
	# IP address search box
	private function ipAddressSearchBox (&$html)
	{
		# Run the form module
		$form = new form (array (
			'displayRestrictions' => false,
			'get' => true,
			'name' => false,
			'nullText' => false,
			'div' => 'ultimateform miniform',
			'submitTo' => $this->baseUrl . '/search/',
			'display'		=> 'template',
			'displayTemplate' => '{[[PROBLEMS]]}' /* Slightly hacky way of ensuring the problems list doesn't appear twice on the page */ . '<p>{ipaddress} {[[SUBMIT]]}</p>',
			'submitButtonText' => 'Search!',
			'submitButtonAccesskey' => false,
			'formCompleteText' => false,
			'requiredFieldIndicator' => false,
			'reappear' => true,
		));
		$form->search (array (
			'name'		=> 'ipaddress',
			'size'		=> 30,
			'maxlength'	=> 15,
			'title'		=> 'IP address',
			'required'	=> true,
			'placeholder' => 'IP address used by machine',
			'autofocus'	=> false,
			'prepend'	=> '<img src="/images/icons/magnifier.png" alt="" class="icon"> ',
			'autocomplete' => $this->dataUrl . '?field=ipaddress',
			'autocompleteOptions' => array ('delay' => 0, ),
		));
		
		# Process the form
		$result = $form->process ($html);
		
		# Return the result
		return $result;
	}
	
	
	# Search facility
	public function search ()
	{
		# Define the codings (lookup values)
		$codings = array (
			'typeId'		=> $this->getTypes (),
			'locationId'	=> $this->getLocations (),
		);
		
		# Create settings for multisearch
		$settings = array (
			'description'						=> strtolower ($this->settings['description']),
			'databaseConnection'				=> $this->databaseConnection,
			'baseUrl'							=> $this->baseUrl . "/{$this->action}/",
			'database'							=> $this->settings['database'],
			'table'								=> $this->settings['table'],
			'dataBindingParameters'				=> $this->machineDatabindingSettings (),
			'orderBy'							=> 'id',
			'mainSubjectField'					=> 'model',
			'enableSimpleSearch'				=> false,	// Simple search doesn't make much sense for this application, as it only searches through the mainSubjectField, and there is already a simple search
			// 'excludeFields' is already appearing through $dataBindingParameters
			'showFields'						=> array (),
			'recordLink'						=> $this->baseUrl . '/machines/%id/edit.html',
//			'paginationRecordsPerPage'			=> $this->settings['paginationRecordsPerPage'],
//			'searchPageInQueryString'			=> true,
//			'ignoreKeys'						=> array ('do'),
//			'exportingEnabled'					=> false,
			'headingLevel'						=> false,
//			'resultsContainerClass'				=> false,
//			'resultRenderer'					=> array ($this, 'dataListing'),
			'codings'							=> $codings,
			'fixedConstraintSql'				=> $this->excludeDecommissionedSql,
		);
		
		# Load and run the multisearch facility
		require_once ('multisearch.php');
		$multisearch = new multisearch ($settings);
		$html = $multisearch->getHtml ();
		
		# Show the HTML
		echo $html;
	}
	
	
	# Search (file export wrapper, which acts the same but is via a different URL and route so that it can run in export mode)
	public function searchExport ()
	{
		return $this->search ();
	}
	
	
	# Main machine (computer) editing section, substantially delegated to the sinenomine editing component
	public function machines ($showDecommissioned = false)
	{
		# Start the HTML
		$html = '';
		
		# On the index page, provide a link to decommissioned machines
		if (!isSet ($_GET['do'])) {
			$html .= "\n<p class=\"decommissioned\"><a href=\"{$this->baseUrl}/machines/decommissioned.html\">See decommissioned machines</a></p>";
		}
		
		# Get the databinding attributes
		$databindingSettings = $this->machineDatabindingSettings ();
		
		# Add sinenomine settings
		$sinenomineSettings = $databindingSettings;
		$sinenomineSettings['successfulRecordRedirect'] = true;
		$sinenomineSettings['pagination'] = false;
		$sinenomineSettings['simpleJoin'] = true;
		$sinenomineSettings['moveDeleteToEnd'] = true;
		$sinenomineSettings['callback'] = array ($this->settings['database'] => array ($this->settings['table'] => array ($this, 'machineCallback')));
		
		# On the non- per-machine pages (i.e. index and search), sort by IP address by default
		#!# Sinenomine needs a better API to handle this - orderby seems to be broken
		if (!isSet ($_GET['do']) || ($_GET['do'] == 'search')) {
			if (!isSet ($_GET['orderby'])) {
				$_GET['orderby'] = 'ipaddress';
			}
		}
		
		# On the non- per-machine pages (i.e. index and search), add a constraint for whether to show decommissioned machines
		if (!isSet ($_GET['do']) || ($_GET['do'] == 'search')) {
			$sinenomineSettings['constraint'] = array ($this->settings['database'] => array ($this->settings['table'] => ($showDecommissioned ? $this->includeDecommissionedSql : $this->excludeDecommissionedSql)));
			if ($showDecommissioned) {
				$_GET['orderby'] = 'decommissionedDate';
				$_GET['direction'] = 'desc';
			}
		}
		
		# Show warning if required
		if (isSet ($_GET['record']) && isSet ($_GET['do']) && ($_GET['do'] == 'delete')) {
			$html .= "\n<div class=\"graybox\">";
			$html .= "\n<p class=\"warning\">Note: this should not be used for decommissioning machines - only for erasing mistakes.</p>";
			$html .= "\n</div>";
		}
		
		# Delegate to the standard function for editing
		$html .= $this->editingTable (__FUNCTION__, $databindingSettings['attributes'], 'ultimateform', false, $sinenomineSettings);
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to show a list of decommissioned machines
	public function decommissioned ()
	{
		return $this->machines ($showDecommissioned = true);
	}
	
	
	# Callback method for machine updating
	public function machineCallback ($record, &$errorHtml = '')
	{
		# Update the machine DNS value
		$record = $this->updateMachineDnsValue ($record);
		
		# Return the record
		return $record;
	}
	
	
	# Update the machine DNS value
	private function updateMachineDnsValue ($record)
	{
		# Look up the DNS name(s)
		$dnsNames = array ();
		foreach ($_POST['form'] as $field => $value) {
			if (preg_match ('/^ipaddress_/', $field)) {
				$dnsNames[] = gethostbyaddr ($value);
			}
		}
		
		# Augment the record
		$record['dnsName'] = implode (', ', $dnsNames);
		
		# Return the record
		return $record;
	}
	
	
	# IP address editing section, substantially delegated to the sinenomine editing component
	public function ipaddresses ()
	{
		# Delegate to the standard function for editing
		$sinenomineExtraSettings = array (
			'simpleJoin'	=> true,
			'pagination'	=> false,
		);
		echo $this->editingTable (__FUNCTION__, array (), 'graybox lines', false, $sinenomineExtraSettings);
	}
	
	
	# Types editing section, substantially delegated to the sinenomine editing component
	public function locations ()
	{
		# Delegate to the standard function for editing
		$sinenomineExtraSettings = array (
			'simpleJoin'	=> true,
			'pagination'	=> false,
		);
		echo $this->editingTable (__FUNCTION__, array (), 'graybox lines', false, $sinenomineExtraSettings);
	}
	
	
	# Types editing section, substantially delegated to the sinenomine editing component
	public function types ()
	{
		# Delegate to the standard function for editing
		$sinenomineExtraSettings = array (
			'simpleJoin'	=> true,
			'pagination'	=> false,
		);
		echo $this->editingTable (__FUNCTION__, array (), 'graybox lines', false, $sinenomineExtraSettings);
	}
	
	
	# Add a new machine
	public function addmachine ()
	{
		# Start the HTML
		$html = '';
		
		# Show a template selection form (which otherwise returns an empty array
		$data = $this->templateSelectionForm ($html);
		
		# Create the machine form or end
		if (!$result = $this->machineForm ($html, true, $data)) {
			echo $html;
			return false;
		}
		
		# Insert the data
		$this->databaseConnection->insert ($this->settings['database'], $this->settings['table'], $result);
		
		# Redirect to machine page, resetting the HTML
		$id = $this->databaseConnection->getLatestId ();
		$location = $_SERVER['_SITE_URL'] . $this->baseUrl . "/machines/{$id}/";
		$html = application::sendHeader (302, $location, $redirectMessage = true);
		
		#!# Set flash
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to create a template selection form
	private function templateSelectionForm (&$html)
	{
		# By default, load an empty form
		$data = array ();
		
		# Get the templates
		if (!$templates = $this->getTemplates ()) {return $data;}
		
		# Start the HTML
		$html = '';
		
		# Create the form
		$form = new form (array (
			'displayRestrictions' => false,
			'name' => 'template',
			'nullText' => false,
			'display'		=> 'template',
			'displayTemplate' => "{[[PROBLEMS]]}<p>Optionally pre-load from a <a href=\"{$this->baseUrl}/templates/\">template</a>: {template} {[[SUBMIT]]} <span class=\"faded\">(Will remove any data below)</span></p>",
			'submitButtonText' => 'Load',
			'submitButtonAccesskey' => false,
			'formCompleteText' => false,
			'requiredFieldIndicator' => false,
			'reappear' => true,
		));
		$form->select (array (
			'name'	=> 'template',
			'title'	=> 'Optionally pre-load from template',
			'required' => true,
			'values' => array_keys ($templates),
		));
		if ($result = $form->process ($html)) {
			
			# Load the data
			$chosenTemplate = $result['template'];
			$data = $templates[$chosenTemplate];
		}
		
		# Return the data (either empty, or the selected template)
		return $data;
	}
	
	
	# Function to set the standard dataBinding defaults for machine editing
	private function machineDatabindingSettings ($templateMode = true, $data = array ())
	{
		# Define the dataBinding attributes
		$attributes = array (
			'ipaddress' => array ('heading' => array (3 => 'Network', ), 'size' => 15, ),
			'dnsName' => array ('editable' => false, ),
			'typeId' => array ('heading' => array (3 => 'Hardware', )),
			'locationId' => array ('heading' => array (3 => 'Location and user', ), ),
			'os' => array ('heading' => array (3 => 'Operating system', )),
			'officeVersion' => array ('heading' => array (3 => 'Key software', )),
			'serialnumber' => array ('heading' => array (3 => 'Identifiers', )),
			'notes' => array ('heading' => array (3 => 'Notes', )),
			'macaddress' => array ('size' => 25),
			'owner' => array ('heading' => array (3 => 'Audit', )),
			'commissionedDate' => array ('picker' => true, ),
			'decommissionedDate' => array ('picker' => true, ),
		);
		
		# In template mode, the MAC address field can be incomplete
		if ($templateMode) {
			$attributes['macaddress']['regexp'] = '^[0-9a-fA-F][0-9a-fA-F][:-][0-9a-fA-F][0-9a-fA-F][:-][0-9a-fA-F][0-9a-fA-F][:-][0-9a-fA-F][0-9a-fA-F][:-][0-9a-fA-F][0-9a-fA-F][:-][0-9a-fA-F][0-9a-fA-F]$';
		}
		
		# Add on autocomplete for all
		$fields = $this->databaseConnection->getFields ($this->settings['database'], $this->settings['table']);
		$fieldsAutocompleteDisabled = array ('id', 'typeId', 'locationId', 'macaddress', 'commissionedDate', 'decommissionedDate', 'tag', );
		foreach ($fields as $field => $fieldAttributes) {
			if (in_array ($field, $fieldsAutocompleteDisabled)) {continue;}
			$attributes[$field]['autocomplete'] = $this->dataUrl . '?field=' . $field;
			$attributes[$field]['autocompleteOptions'] = array ('delay' => 0, );	// See: http://jqueryui.com/demos/autocomplete/#remote (this is the new plugin)
		}
		
		# Add expandability to most fields
		#!# Need to disable for search?
		$expandableFields = array ('ipaddress', 'monitor', 'processor', 'memory', 'harddisk', 'videocard', 'networkcard', 'user', 'os', );
		foreach ($fields as $field => $fieldAttributes) {
			if (!in_array ($field, $expandableFields)) {continue;}
			$attributes[$field]['expandable'] = $this->settings['expandableCharacter'];
		}
		
		# Compile the settings
		$databindingSettings = array (
			'database' => $this->settings['database'],
			'table' => $this->settings['table'],
			'simpleJoin' => true,
			'lookupFunctionParameters' => array ($showKeys = false, $orderBy = array ('id'), $sort = false, false, $firstOnly = false),
			'intelligence' => true,
			'data' => $data,
			'attributes' => $attributes,
		);
		
		# Return the settings
		return $databindingSettings;
	}
	
	
/*
	# Function to get a list of IP addresses
	private function getIpAddresses ()
	{
		# Get the IP addresses; no need to use SELECT DISTINCT as the ipAddress field already has a UNIQUE index
		$query = "SELECT TRIM(ipAddress) AS value, TRIM(ipAddress) AS name FROM {$this->settings['database']}.ipaddresses ORDER BY INET_ATON(TRIM(ipAddress));";
		$ipAddresses = $this->databaseConnection->getPairs ($query);
		
		# Return the data
		return $ipAddresses;
	}
*/
	
	
	# Function to create a form for a machine manually
	private function machineForm (&$html, $templateMode = true, $data = array (), $name = false)
	{
		# Create the form
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'displayRestrictions' => false,
			'formCompleteText' => false,
			'unsavedDataProtection' => true,
			'nullText' => false,
			'autofocus' => true,
		));
		
		# Set no fields to exclude by default
		$exclude = array ();
		
		# Define the settings
		$databindingSettings = $this->machineDatabindingSettings ($templateMode, $data);
		
		# Template mode
		if (!$templateMode) {
			
			# Set to exclude fields not relevant to a template
			$databindingSettings['exclude'] = array (
				// Do not change these without checking with the Computer Officers!
				// If changing these, make sure that the 'heading' specifications in dataBinding attributes below will not be affected
				'ipaddress',
				'user',
				'tag', 
				// macaddress has special handling below
			);
			
			# Force all fields to be non-required
			$fields = $this->databaseConnection->getFields ($this->settings['database'], $this->settings['table']);
			foreach ($fields as $fieldname => $field) {
				$databindingSettings['attributes'][$fieldname]['required'] = false;
			}
			
			# Get the current templates as a name list, removing the current one (as that would prevent it being edited)
			$templates = $this->getTemplates ();
			unset ($templates[$name]);
			$templates = array_keys ($templates);
			
			# Add in the template name
			$form->heading ('3', ($data ? 'Name of this template' : 'What name do you want to give this template?'));
			$form->input (array (
			    'name'		=> 'name',
			    'title'		=> 'Template name',
			    'required'	=> true,
			    'maxlength'	=> 255,
				'current'	=> $templates,	// i.e. Other template names not including the current one
				'default'	=> ($data ? $name : false),
			));
			$form->heading ('p', 'Now add below as many fields as you wish, in order to create a template:');
		}
		
		# Databind the form
		$form->dataBinding ($databindingSettings);
		// $form->setOutputScreen ();
		
		# Get the result
		$result = $form->process ($html);
		
		# Return the result
		return $result;
	}
	
	
	# Function to provide auto-complete functionality
	public function data ()
	{
		# End if no query or no field or too short
		if (!isSet ($_GET['field']) || !strlen ($_GET['field'])) {return false;}
		if (!isSet ($_GET['term']) || !strlen ($_GET['term']) || (strlen ($_GET['term']) < 3)) {return false;}
		
		# Obtain the query and the field
		$field = $_GET['field'];
		$term = $_GET['term'];
		
		# Get the fields and ensure the requested field exists
		$fields = $this->databaseConnection->getFields ($this->settings['database'], $this->settings['table']);
		if (!isSet ($fields[$field])) {return false;}
		
		# Get the unique values in the table for this field
		switch ($field) {
			
			# Username field uses data from the people database, sent back as value&label fields
#!# Replace with HTTP retrieval
			case 'user':
				$query = "SELECT
						username AS value,
						CONCAT(username,' (',forename,' ',surname,')') AS label
					FROM people.people
					WHERE
						   username LIKE :term
						OR forename LIKE :term
						OR surname LIKE :term
						OR CONCAT(forename,' ',surname) LIKE :term
					ORDER BY label;";
				if (!$data = $this->databaseConnection->getData ($query, false, true, array ('term' => $term . '%'))) {return false;}
				
				break;
				
			# IP addresses come from a separate table
			case 'ipaddress':
				$query = "SELECT
					ipAddress as value,
					ipAddress as label
				FROM ipaddresses
				WHERE ipAddress LIKE :term
				ORDER BY INET_ATON(ipAddress);";
				if (!$data = $this->databaseConnection->getData ($query, false, true, array ('term' => $term . '%'))) {return false;}
				break;
				
			default:
				$query = "SELECT DISTINCT `{$field}` FROM {$this->settings['database']}.{$this->settings['table']} WHERE `{$field}` REGEXP '[[:<:]]{$term}' ORDER BY `{$field}`;";
				// $query = "SELECT DISTINCT `{$field}` FROM {$this->settings['database']}.{$this->settings['table']} WHERE `{$field}` LIKE '{$term}%' ORDER BY `{$field}`;";
				if (!$data = $this->databaseConnection->getPairs ($query)) {return false;}
		}
		
		# Arrange the data
		$json = json_encode ($data);
		
		# Send the text
		echo $json;
	}
	
	
	# Machine templates - home page
	public function machinetemplates ()
	{
		# Get the current templates
		$templates = $this->getTemplates ();
		
		# Start the HTML
		$html  = "\n<p>In this section, you can set up and manage machine templates that can be used for creating main machine records easily.</p>";
		
		# Addition
		$html .= "\n<div class=\"graybox\">";
		$html .= "\n<h2>Add a template</h2>";
		$html .= "\n<p><a class=\"actions\" href=\"{$this->baseUrl}/templates/add.html\">" . $this->icon ('computer_add') . " Create a new template</a></p>";
		$html .= "\n</div>";
		
		# Show current
		$html .= "\n<div class=\"graybox\">";
		$html .= "\n<h2>Current templates</h2>";
		$html .= '%TEMPLATESLIST%';
		$html .= "\n</div>";
		
		# Show deletion form, and reload the list
		if ($templates) {
			$html .= "\n<div class=\"graybox\">";
			$html .= "\n<h2>Delete a template</h2>";
			$html .= $this->templateDeletionForm (array_keys ($templates));
			$templates = $this->getTemplates ();	// Reload, since it may have changed
			$html .= "\n</div>";
		}
		
		# Load the template list into the placeholder
		$templatesListHtml = $this->templatesListHtml ($templates);
		$html = str_replace ('%TEMPLATESLIST%', $templatesListHtml, $html);
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to arrange a list of templates as an HTML list
	private function templatesListHtml ($templates)
	{
		# End if none
		if (!$templates) {
			return $html = "\n<p>No templates have yet been created.</p>";
		}
		
		# Compile the HTML
		$list = array ();
		foreach ($templates as $name => $data) {
			$list[] = $this->templateLink ($name) . htmlspecialchars (" - {$data['type']}");
		}
		$html = application::htmlUl ($list);
		
		# Return the list
		return $html;
	}
	
	
	# Function to create a template deletion form
	private function templateDeletionForm ($templates)
	{
		# Start the HTML
		$html = '';
		
		# Create the form
		$form = new form (array (
			'name' => 'delete',
			'submitTo' => '#delete',
			'formCompleteText' => false,
			'display' => 'paragraphs',
			'requiredFieldIndicator' => false,
		));
		$form->select (array (
			'name'	=> 'name',
			'title'	=> 'Select template to remove',
			'required' => true,
			'values' => $templates,
		));
		$form->input (array (
		    'name'		=> 'confirm',
		    'title'		=> 'Please type the template name to confirm',
		    'required'	=> true,
			'size'		=> 20,
		));
		if ($unfinalisedData = $form->getUnfinalisedData ()) {
			if ($unfinalisedData['name'] && $unfinalisedData['confirm']) {
				if ($unfinalisedData['name'] != $unfinalisedData['confirm']) {
					$form->registerProblem ('mismatch', 'The name confirmation does not match.');
				}
			}
		}
		if ($result = $form->process ($html)) {
			if ($this->databaseConnection->delete ($this->settings['database'], 'templates', array ('name' => $result['name']))) {
				$html = "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" alt=\"Tick\" /> " . htmlspecialchars ($result['name']) . " has been deleted. <a href=\"\">Reset page.</a></p>";
			} else {
				$html = "\n<p class=\"warning\">There was a problem deleting the template.</p>";
			}
		}
		
		# Return the HTML
		return $html;
	}
	
	
	
	# Function to get the current templates
	private function getTemplates ()
	{
		# Get the template data, which is a set of sharded key-value pairs
		$data = $this->databaseConnection->select ($this->settings['database'], 'templates', array (), array (), true, 'name');
		
		# Get the types
		$types = $this->getTypes ();
		
		# Group by name
		$templates = array ();
		foreach ($data as $index => $entry) {
			$name = $entry['name'];
			$attribute = $entry['attribute'];
			$value = $entry['value'];
			$templates[$name][$attribute] = $value;
		}
		
		# Add in the type name
		foreach ($templates as $name => $attributes) {
			if (isSet ($attributes['typeId'])) {
				$templates[$name]['type'] = $types[$attributes['typeId']];
			}
		}
		
		# Return the names
		return $templates;
	}
	
	
	# Function to get the types
	private function getTypes ()
	{
		return $this->databaseConnection->selectPairs ($this->settings['database'], 'types');
	}
	
	
	# Function to get the locations
	private function getLocations ()
	{
		return $this->databaseConnection->selectPairs ($this->settings['database'], 'locations', array (), array ('id', "CONCAT(building, ' ', floor) AS location"));
	}
	
	
	# Templates - home page
	public function templateadd ()
	{
		# Start the HTML
		$html = '';
		
		# Create the machine form or end
		if (!$result = $this->machineForm ($html, false)) {
			echo $html;
			return false;
		}
		
		# Save the data
		$html .= $this->saveTemplate ($result);
		
		# Show the HTML
		echo $html;
	}
	
	
	# Templates - view/edit
	public function templateedit ($name)
	{
		# Start the HTML
		$html = '';
		
		# Get the current item
		if (!$name = (isSet ($_GET['item']) ? $_GET['item'] : false)) {
			$this->page404 ();
			return false;
		}
		
		# Validate the name
		$templates = $this->getTemplates ();
		if (!isSet ($templates[$name])) {
			application::sendHeader (404);
			$html .= "\n<p>There is no such template.</p>";
			$html .= "\n<p>Please select a valid template name from the <a href=\"{$this->baseUrl}/templates/\">list of templates</a>.</p>";
			echo $html;
			return false;
		}
		
		# Get the data for this template
		$template = $templates[$name];
		
		# Create the machine form or end
		if (!$result = $this->machineForm ($html, false, $template, $name)) {
			echo $html;
			return false;
		}
		
		# Save the data
		$html .= $this->saveTemplate ($result, $name);
		
		# Show the HTML
		echo $html;
	}
	
	
	# Helper function to save the template data
	public function saveTemplate ($data, $clearCurrent = false)
	{
		# Extract the name
		$name = $data['name'];
		unset ($data['name']);
		
		# Take the data, and filter so that only submitted values are present
		foreach ($data as $key => $value) {
			if (!strlen ($value)) {
				unset ($data[$key]);
			}
		}
		
		# Add each value into the templates database
		$dataSet = array ();
		foreach ($data as $key => $value) {
			$dataSet[] = array (
				'name'		=> $name,
				'attribute'	=> $key,
				'value'		=> $value,
			);
		}
		
		#!# Migrate to updateMany ?
		
		# If there is current data, wipe it first
		if ($clearCurrent) {
			$this->databaseConnection->delete ($this->settings['database'], 'templates', array ('name' => $clearCurrent));
		}
		
		# Insert the data
		$this->databaseConnection->insertMany ($this->settings['database'], 'templates', $dataSet);
		
		# Confirm
		$html  = "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" alt=\"Tick\" /> The template <strong>" . $this->templateLink ($name) . '</strong> has been saved.</p>';
		$html .= "\n<p>Return to the <a href=\"{$this->baseUrl}/templates/\">list of templates</a>.</p>";
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create a template link URL
	private function templateLink ($name)
	{
		# Construct the link
		$link = $this->baseUrl . '/templates/' . htmlspecialchars (urlencode ($name)) . '/';
		$html = "<a href=\"" . $link . '">' . htmlspecialchars ($name) . '</a>';
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create an index of attributes
	public function attributes ($attribute = false)
	{
		# Start the HTML
		$html = '';
		
		# Get the available listable machine attributes
		$attributes = $this->getListableAttributes ();
		
		# Validate any selected attribute, or end
		if ($attribute) {
			if (!isSet ($attributes[$attribute])) {
				$this->page404 ();
				return false;
			}
		}
		
		# If an attribute is selected, show a drop-list
		$html .= $this->attributeDroplist ($attributes, $attribute);
		
		# Create a listing if no attribute selected
		if (!$attribute) {
			$list = array ();
			foreach ($attributes as $attributeId => $label) {
				$list[$attributeId] = "<a href=\"{$this->baseUrl}/attributes/{$attributeId}/\"><strong>" . htmlspecialchars ($label) . '</strong></a>';
			}
			$html .= "\n<h2>Index of attributes</h2>";
			$html .= "\n<p>In this section, you can view all the unique values for each attribute.</p>";
			$html .= "\n<p>Please select an attribute:</p>";
			$html .= application::htmlUl ($list, 0, 'boxylist');
			echo $html;
			return false;
		}
		
		# Get a list of distinct values for the selected machine attribute
		$values = $this->getAttributeValues ($attribute);
		
		# Validate any selected value, or end
		$value = false;
		if (isSet ($_GET['value'])) {
			
			# Overwrite the value with a correct version that maintains URL encoding properly, specifically of + signs, by emulating it from the original REQUEST_URI
			# The problem arises because mod_rewrite decodes at the point of shifting the path fragment (e.g. "/attributes/harddisk/80gb+system+%2B+2+x+250gb+data/" results in "value=80gb system   2 x 250gb data"
			# See: http://stackoverflow.com/a/10999987/180733 , in particular: "In fact it is impossible to do using a rewrite rule alone. Apache decodes the URL before putting it through rewrite, but it doesn't understand plus signs"
			preg_match ('|^' . $this->baseUrl . '/attributes/([^/]+)/(.+)/|', $_SERVER['REQUEST_URI'], $matches);	// e.g. /attributes/harddisk/80gb+system+%2B+2+x+250gb+data/
			$_GET['value'] = urldecode ($matches[2]);	// Overwrite, with the URLdecoding doing what Apache would natively do
			
			# Now do standard processing
			$valueLowercase = strtolower ($_GET['value']);
			if (!application::iin_array ($valueLowercase, $values, NULL, $value)) {	// Case-insensitive version of in_array
				$this->page404 ();
				return false;
			}
		}
		
		# Create a listing if no value selected
		if (!$value) {
			$list = array ();
			natsort ($values);
			foreach ($values as $value) {
				$list[] = "<a href=\"{$this->baseUrl}/attributes/{$attribute}/" . htmlspecialchars (urlencode (strtolower ($value))) . '/">' . htmlspecialchars ($value) . '</a>';	// urlencode means + becomes %2B and space becomes + (rather than ugly %20); htmlspecialchars() is purely for safe HTML purposes and does not affect the actual transmitted URL
			}
			$html .= "\n<h2>" . htmlspecialchars ($attributes[$attribute]) . " &mdash; listing of unique values</h2>";
			if ($values) {
				$html .= "\n<p>This list shows each unique value of <strong>" . htmlspecialchars ($attributes[$attribute]) . "</strong>:</p>";
				$html .= "\n<p>Please make efforts to merge similar entries that should be the same.</p>";
				$html .= "\n<p>Please select a value:</p>";
				$html .= application::htmlUl ($list);
			} else {
				$html .= "\n<p>There are no <strong>" . htmlspecialchars ($attributes[$attribute]) . "</strong> items yet.</p>";
			}
			echo $html;
			return false;
		}
		
		# Show the list of machines having this attribute value
		$machines = $this->selectMachines ($attribute, $valueLowercase);
		
		# Convert to HTML and enable newlines
		foreach ($machines as $id => $machine) {
			foreach ($machine as $key => $machineValue) {
				$machines[$id][$key] = nl2br (htmlspecialchars ($machineValue));
			}
		}
		
		# Make each ID a link
		foreach ($machines as $id => $machine) {
			$url = $this->baseUrl . "/machines/{$id}/edit.html";
			$machines[$id]['id'] = "<a href=\"{$url}\">{$id}</a>";
		}
		
		# Summary
		$html .= "\n<h2>" . htmlspecialchars ($attributes[$attribute]) . ': ' . htmlspecialchars ($value) . '</h2>';
		$html .= "\n<p>Matches for <a href=\"{$this->baseUrl}/attributes/{$attribute}/\">" . htmlspecialchars ($attributes[$attribute]) . '</a> &raquo; <strong>' . htmlspecialchars ($value) . '</strong>:</p>';
		$total = count ($machines);
		$html .= "\n<p>There " . ($total == 1 ? 'is one item' : "are {$total} items") . ":</p>";
		
		# Render the list as a table
		$headings = $this->databaseConnection->getHeadings ($this->settings['database'], $this->settings['table']);
		$html .= application::htmlTable ($machines, $headings, $class = 'sinenomine searchresult lines sortable" id="sortable', $keyAsFirstColumn = false, false, $allowHtml = true, false, $addCellClasses = true);
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to create a list of all listable machine attributes
	private function getListableAttributes ()
	{
		# Get the headings from the database
		$attributes = $this->databaseConnection->getHeadings ($this->settings['database'], $this->settings['table']);
		
		# Define wanted fields
		$desiredAttributes = array (
			'model',
			'monitor',
			'processor',
			'memory',
			'harddisk',
			'videocard',
			'networkcard',
			'os',
			'sitevariable',
			'image',
		);
		
		# Filter to desired fields
		$attributes = application::arrayFields ($attributes, $desiredAttributes);
		
		# Return the list
		return $attributes;
	}
	
	
	# Function to create an attribute droplist
	private function attributeDroplist ($attributes, $attribute)
	{
		# Create the droplist
		$droplist = array ();
		$droplist["{$this->baseUrl}/attributes/"] = 'Select attribute:';
		foreach ($attributes as $attributeId => $label) {
			$url = "{$this->baseUrl}/attributes/{$attributeId}/";
			$droplist[$url] = htmlspecialchars ($label);
		}
		$selected = "{$this->baseUrl}/attributes/" . ($attribute ? "{$attribute}/" : '');
		
		# Compile the HTML and register a processor
		$html = application::htmlJumplist ($droplist, $selected, $this->baseUrl . '/attributes/', $name = 'attributesdroplist', $parentTabLevel = 0, $class = 'attributesdroplist right', false);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create a list of all listable machine attributes
	private function getAttributeValues ($attribute)
	{
		# Get the data
		#!# Note that using prepared statements does not appear to work
		$query = "SELECT
				DISTINCT({$attribute})
			FROM {$this->settings['database']}.{$this->settings['table']}
			WHERE
				    {$attribute} != ''
				AND {$attribute} IS NOT NULL
				AND {$this->excludeDecommissionedSql}
			ORDER BY {$attribute}
		;";
		$valuesRaw = $this->databaseConnection->getPairs ($query);
		
		# Dereference multiple values
		$values = array ();
		foreach ($valuesRaw as $index => $value) {
			$value = trim ($value);
			if (substr_count ($value, $this->settings['expandableCharacter'])) {
				$parts = explode ($this->settings['expandableCharacter'], $value);
				$values = array_merge ($values, $parts);
			} else {
				$values[] = $value;
			}
		}
		
		# Order by attribute
		sort ($values);
		
		# Return the list
		return $values;
	}
	
	
	# Function to create a list of all machines with a matching attribute value
	private function selectMachines ($attribute, $valueLowercase)
	{
		# Define prepared statement values
		$preparedStatementValues = array (
			'attribute'		=> $valueLowercase,
		);
		
		# Obtain the data, case-insensitively, optionally bounded by start/finish newline; CHAR(10) is \n
		$query = "SELECT
			*
		FROM {$this->settings['database']}.{$this->settings['table']}
		WHERE (
			   {$attribute} = :attribute
			OR {$attribute} LIKE CONCAT(:attribute, CHAR(10), '%')					/* First line of string */
			OR {$attribute} LIKE CONCAT('%', CHAR(10), :attribute)					/* Last line of string */
			OR {$attribute} LIKE CONCAT('%', CHAR(10), :attribute, CHAR(10), '%')	/* Middle line in string */
			)
			AND {$this->excludeDecommissionedSql}
		;";
		$data = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['table']}", true, $preparedStatementValues);
		
		# Return the data
		return $data;
	}
	
	
	# Function to force a refresh of the DNS lookups
	public function refreshdns ()
	{
		# Start the HTML
		$html = '';
		
		# Obtain confirmation from the user
		$message = 'Refresh DNS?';
		$confirmation = 'Yes, refresh DNS';
		if (!$this->areYouSure ($message, $confirmation, $html)) {
			echo $html;
			return false;
		}
		
		# Get all current addresses
		$ipAddresses = $this->databaseConnection->selectPairs ($this->settings['database'], 'machines', array (), array ('id', 'ipaddress'));
		
		# Perform lookups
		$dnsNames = array ();
		foreach ($ipAddresses as $id => $ipAddress) {
			$dnsNames[$id]['dnsName'] = gethostbyaddr ($ipAddress);
		}
		
		# Update the data
		#!# Can't yet get a proper result status
		$this->databaseConnection->updateMany ($this->settings['database'], 'machines', $dnsNames);
		
		# Confirm, resetting the HTML
		$html = "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" alt=\"Tick\" /> The DNS names have been refreshed. <a href=\"{$this->baseUrl}/machines/\">Browse machines.</a></p>";
		
		# Show the HTML
		echo $html;
	}
	
	
	# Initial import facility
	public function import ()
	{
		# Start the HTML
		$html = '';
		
		# Define the upload directory
		$directory = $_SERVER['DOCUMENT_ROOT'] . $this->baseUrl . '/';
		
		# Create the upload form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'div' => 'graybox lines',
			'formCompleteText' => false,
		));
		$form->heading ('', '<p class="warning"><strong>Warning: submitting this form will completely remove all current data in the system!</strong></p>');
		$form->upload (array (
			'name'				=> 'data',
			'title'				=> 'Spreadsheet',
			'directory'			=> $directory,
			'description'		=> 'Must be in Excel 2007 format',
			'allowedExtensions'	=> array ('xlsx'), 
			'forcedFileName'	=> 'upload', 
			'required'			=> true,
		));
		if (!$result = $form->process ($html)) {
			echo $html;
			return;
		}
		
		# Convert the file to CSV
		require_once ('csv.php');
		csv::xls2csv ($directory, $directory);
		
		# Read the CSV file
		$data = application::getCsvData ($directory . 'upload.csv', NULL, $autoAssignKeys = true);
		
		# Remove the upload and generated files
		unlink ($directory . 'upload.xlsx');
		unlink ($directory . 'upload.csv');
		
		# Trim all cells
		foreach ($data as $key => $record) {
			foreach ($record as $field => $value) {
				$data[$key][$field] = trim ($value);
			}
		}
		
		# Rename the headings
		$headings = array (
			'ipAddress'		=> 'ipaddress',
			'DNS Name'		=> NULL,
			'type (network/server/printer/desktop/laptop/webcam/scanner/virtual-machine/-)'		=> 'typeId',
			'manufacturer'		=> 'manufacturer',
			'model (macbook/Thinkpad/Vaio/etc.) or description'		=> 'model',
			'optional:processor'		=> 'processor',
			'optional:memory'		=> 'memory',
			'Hard disk'		=> 'harddisk',
			'Video Card'		=> 'videocard',
			'Network Card'		=> 'networkcard',
			'notes 1'		=> 'notes',
			'locationBuilding'		=> 'locationBuilding',	// Merged below as lookup
			'locationFloor'		=> 'locationFloor',			// Merged below as lookup
			'locationRoom'		=> 'room',
			'Owner (Dept, grant, personal)'		=> 'owner',
			'End Userid'		=> 'user',
			'_X_End Username'		=> NULL,
			'operating system'		=> 'os',
			'Office Version'		=> 'officeVersion',
			'Adobe Software'		=> 'adobeSoftware',
			'Site variable'		=> 'sitevariable',
			'Image Version'		=> 'image',
			'Tag'		=> 'tag',
			'Box s/n'		=> 'serialnumber',
			'Mac address'	=> 'macaddress',
		);
		$data = application::array_rename_dataset_fields ($data, $headings);
		
		# Hard disk - split by + or ,
		foreach ($data as $key => $record) {
			$data[$key]['harddisk'] = str_replace (array (' + ', ', '), "\n", $data[$key]['harddisk']);
		}
		
		# Extract the IP address list
		$ipAddresses = application::array_extract_dataset_field ($data, 'ipaddress');
		$inserts = array ();
		$i = 0;
		foreach ($ipAddresses as $ipAddress) {
			$i++;	// MySQL primary key values should start at 1
			$inserts[$i] = array (
				'id'			=> $i,
				'ipAddress'		=> $ipAddress,
			);
		}
		if (!$this->databaseConnection->truncate ($this->settings['database'], 'ipaddresses')) {
			application::dumpData ($this->databaseConnection->error ());
		}
		if (!$this->databaseConnection->insertMany ($this->settings['database'], 'ipaddresses', $inserts)) {
			application::dumpData ($this->databaseConnection->error ());
		}
		
		# Convert building locations (building plus floor) (which are assumed to be pre-populated in the database already) to IDs
		$locationIds = $this->databaseConnection->selectPairs ($this->settings['database'], 'locations', array (), array ("CONCAT(building,':',floor) AS location", 'id'));
		foreach ($data as $key => $record) {
			$locationId = $data[$key]['locationBuilding'] . ':' . $data[$key]['locationFloor'];
			unset ($data[$key]['locationBuilding']);
			unset ($data[$key]['locationFloor']);
			$data[$key]['locationId'] = NULL;	// Default
			if ($locationId == ':') {continue;}	// Skip empty
			if (!isSet ($locationIds[$locationId])) {
				$html .= sprintf ("<p class=\"warning\">There is no location <em>%s</em>, as found in record #{$key}.</p>", htmlspecialchars ($locationId));
				echo $html;
				return false;
			}
			$data[$key]['locationId'] = $locationIds[$locationId];
		}
		
		# Convert types (which are assumed to be pre-populated in the database already) to IDs
		$typeIds = $this->databaseConnection->selectPairs ($this->settings['database'], 'types', array (), array ('type', 'id'));
		foreach ($data as $key => $record) {
			$typeId = $data[$key]['typeId'];
			if (!strlen ($typeId)) {continue;}	// Skip empty
			if (!isSet ($typeIds[$typeId])) {
				$html .= sprintf ("<p class=\"warning\">There is no type <em>%s</em>, as found in record #{$key}.</p>", htmlspecialchars ($typeId));
				echo $html;
				return false;
			}
			$data[$key]['typeId'] = $typeIds[$typeId];
		}
		
		# Look up DNS names
		foreach ($data as $key => $record) {
			if (!$dnsName = gethostbyaddr ($record['ipaddress'])) {
				$html .= "<p class=\"warning\">Error converting {$record['ipaddress']} to a DNS name.</p>";
				echo $html;
				return false;
			}
			$data[$key]['dnsName'] = $dnsName;
		}
		
		# Insert the main data
		if (!$this->databaseConnection->truncate ($this->settings['database'], 'machines')) {
			application::dumpData ($this->databaseConnection->error ());
		}
		if (!$this->databaseConnection->insertMany ($this->settings['database'], 'machines', $data)) {
			application::dumpData ($this->databaseConnection->error ());
		}
		
		# Confirm
		$html = "\n<p><img src=\"/images/icons/tick.png\" class=\"icon\" alt=\"Tick\" /> The data has been imported. <a href=\"\">Reset page.</a></p>";
		
		# Show the HTML
		echo $html;
	}
	
	
	# Settings page customisation
	public function settings ($dataBindingSettingsOverrides = array ())
	{
		$dataBindingSettingsOverrides = array (
			'attributes' => array (
			),
		);
		
		# Run the settings page
		parent::settings ($dataBindingSettingsOverrides);
	}
}

?>
