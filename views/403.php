<?php

if (\Input::is_ajax())
{
	echo json_encode(array('message' => '403 Forbidden'));
}
else
{ ?>
403
<?php }