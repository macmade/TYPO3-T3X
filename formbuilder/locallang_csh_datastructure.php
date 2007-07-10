<?php
	/**
	 * Default TCA_DESCR for table 'tx_formbuilder_datastructure'
	 */
	$LOCAL_LANG = Array (
		'default' => Array (
			'title.description' => 'The title of the form.',
			'title.details' => 'The form title will be used to produce a list of the forms presents in a page, if there is more than one.',
			'description.description' => 'The description of the form.',
			'description.details' => 'The text that will appear before the form.',
			'post_message.description' => 'The confirmation message.',
			'post_message.details' => 'This message will appear after the form has been submitted. If the value is empty, it will display a default confirmation message.',
			'be_groups.description' => 'BE-Groups who have access to this form.',
			'be_groups.details' => 'Only the groups selected here will have access to this form in the "Forms" backend module. If there is no group specified, then all groups will have access to the form.',
			'recipients.description' => 'One line by email address.',
			'recipients.details' => 'Enter here the email addresses of the persons who need to be notified when the form has been submitted. Separate each email address by a carriage return.',
			'datasend.description' => 'Send form data to recipients.',
			'datasend.details' => 'If this is checked, the recipients will also receive the form data with notifivation message.',
			'submit.description' => 'Alternate submit label.',
			'submit.details' => 'Enter the text of the submit button. If this is empty, it will display a default value.',
			'destination.description' => 'The storage PID for the new records.',
			'destination.details' => 'You can here choose a page to store the incoming records from the form. If none is specified, the records will be stored on the same page as the form.',
			'preview.description' => 'Preview the form before submitting.',
			'preview.details' => 'If this is checked, it will add a preview feature in the form. Otherwise, the data will be submitted directly.',
			'redirect.description' => 'Redirect to a page after the confirmation.',
			'redirect.details' => 'You can here choose a page to which the user will be redirected after the form has been submitted. If you don\'t none, the user will stay on the same page.',
			'redirect_time.description' => 'The time in seconds before the redirection.',
			'redirect_time.details' => 'Enter here the number of seconds (between 0 and 30) before the redirection. If the value is 0, the user will be redirected instantly.',
			'xmlds.description' => 'Field creation wizard.',
			'xmlds.details' => 'You can here create the fields of your form. Click on the select menu, and choose a field type. Then save the form to view the field options. You have to add a label for each field. The label will be displayed beside the field, in the form.
				
				You will see below a description of the available types, with their own options:
				
				<strong>Text input</strong>
				A classic text input.
				
				The available options are:
				&nbsp;&nbsp;&gt; <b>Required:</b> Check this to force the user to write something in the text input. If the field is empty, the user won\'t be able to submit the form.
				&nbsp;&nbsp;&gt; <b>Eval:</b> You can choose to force the user to write a specified type of data in the text input. The possible value is:
				&nbsp;&nbsp;&nbsp;&nbsp;- <i>Email address:</i> That will check for a valid email address, containing an arobase (@) and a domain (.com, .net, etc). If this is not the case, the user won\'t be able to submit the form.
				&nbsp;&nbsp;&gt; <b>Parse:</b> You can choose to alter the value of the field with specific functions. The possible values are:
				&nbsp;&nbsp;&nbsp;&nbsp;- <i>Integer:</i> Convert the value to an integer.
				&nbsp;&nbsp;&nbsp;&nbsp;- <i>Lowercase:</i> Convert value to lowercase.
				&nbsp;&nbsp;&nbsp;&nbsp;- <i>Uppercase:</i> Convert value to uppercase.
				&nbsp;&nbsp;&nbsp;&nbsp;- <i>No space:</i> Erase any space in the value.
				&nbsp;&nbsp;&nbsp;&nbsp;- <i>Alphabetic:</i> Only alhabetic characters.
				&nbsp;&nbsp;&nbsp;&nbsp;- <i>Numeric:</i> Only numeric characters.
				&nbsp;&nbsp;&nbsp;&nbsp;- <i>Alphanumeric:</i> Only alphabetic and numeric characters.
				&nbsp;&nbsp;&nbsp;&nbsp;- <i>Alphanumeric (with _ and -):</i> Only alphabetic and numeric characters, with _ and -.
				&nbsp;&nbsp;&nbsp;&nbsp;- <i>Trim:</i> Erase any space around the value.
				
				<strong>Textarea</strong>
				A classic textarea zone.
				
				The available options are:
				&nbsp;&nbsp;&gt; <b>Required:</b> Check this to force the user to write something in the textarea. If the field is empty, the user won\'t be able to submit the form.
				
				<strong>Checkboxes</strong>
				A set of checkboxes.
				
				When you create a checkboxes field, you have the possibility to add as many checkboxes as you want. Just go the the select menu under the label, and choose <NEW "Checkbox">. Then save, and enter a value for the checkbox you just created. Repeat this step to produce more checkboxes.
				
				There is no option for that kind of field.
				
				<strong>Radio buttons</strong>
				A set of radio buttons.
				
				When you create a radio buttons field, you have the possibility to add as many radio buttons as you want. Just go the the select menu under the label, and choose <NEW "Radio button">. Then save, and enter a value for the radio button you just created. Repeat this step to produce more radio buttons.
				
				There is no option for that kind of field.
				
				<strong>Select</strong>
				A select menu.
				
				When you create a select field, you have the possibility to add as many select items as you want. Just go the the select menu under the label, and choose <NEW "Select item">. Then save, and enter a value for the select item you just created. Repeat this step to produce more select items.
				
				There is no option for that kind of field.
				
				<strong>Database relation</strong>
				A relation to a database table.
				
				The available options are:
				&nbsp;&nbsp;&gt; <b>Table:</b> The database table to create a relation to.
				&nbsp;&nbsp;&gt; <b>Source page:</b> The page(s) from where to get the records.
				
				<strong>Files</strong>
				A input for uploading files.
				
				The available options are:
				&nbsp;&nbsp;&gt; <b>Types:</b> You can choose to force to user to upload a certain kind of file. If the file type is not correct, the user won\'t be able to submit the form.
				&nbsp;&nbsp;&gt; <b>Maximum size:</b> The maxium size (in kilobytes) of the submitted file. If the size exceeds, the user won\'t be able to submit the form.
				&nbsp;&nbsp;&gt; <b>Number:</b> The number of input to generate, if you want to user to be able to upload more than one file.
				
				<strong>Password</strong>
				A password input.
				
				The available options are:
				&nbsp;&nbsp;&gt; <b>Required:</b> Check this to force the user to write something in the password input. If the field is empty, the user won\'t be able to submit the form.',
				'xmlds.image_descr' => 'Text input
				Textarea
				Checkboxes
				Radio buttons
				Select
				Database relation
				Files
				Password',
				'xmlds.image' => 'EXT:formbuilder/csh/text_input.gif,EXT:formbuilder/csh/textarea.gif,EXT:formbuilder/csh/checkboxes.gif,EXT:formbuilder/csh/radio_buttons.gif,EXT:formbuilder/csh/select.gif,EXT:formbuilder/csh/database_relation.gif,EXT:formbuilder/csh/files.gif,EXT:formbuilder/csh/password.gif',
		),
	);
?>
