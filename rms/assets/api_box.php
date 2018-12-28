<?
set_time_limit(120);
$key = '';

$data = json_decode($_POST['data']);

if($data->key != $key) {
	exit('error A');
}

$command="sudo ";

if($data->order == 'chacun') {
	if($data->module == 'dimmer') $command .= "tdtool -v $data->value -d $data->id ; tdtool -v $data->value -d $data->id";
	if($data->module == 'switch') $command .= "tdtool --$data->value $data->id ; tdtool --$data->value $data->id";
}

if($data->order == 'sound') {
	if($data->jingle) $command .="mpg123 $data->jingle ";
	if($data->type == 'audio') $command .="mpg123 $data->message";
	if($data->type == 'text') $command .="espeak $data->message";
	$command .= $data->message;
}
echo $command;
exec($command, $output, $ret);
//print_r($output);
?>