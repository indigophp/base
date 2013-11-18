<?php

if (\Input::is_ajax())
{
	echo json_encode(array('message' => '401 Unauthorized'));
}
else
{ ?>
401
<?php }