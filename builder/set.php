<h1>set a data</h1>
<input type="text" placeholder="The var: db optional" id="var_" onkeyup="createCode();" />
<input type="text" placeholder="what is the key ?" id="key" onkeyup="createCode();" />
<input type="text" placeholder="what is the id ?" id="id" onkeyup="createCode();" />
<input type="text" placeholder="The data name ?" id="data_key" onkeyup="createCode();" />
<input type="text" placeholder="The data content ?" id="data_value" onkeyup="createCode();" />
<br /><br />Here is the result:<br /><br />
<textarea cols="80" rows="10" id="code" onclick="this.select();">

</textarea>
<br /><br />Don't forget to include the main file and create an instance for <span id="instance">$db</span>
<script type="text/javascript">
var var_ = document.getElementById('var_');
var key = document.getElementById('key');
var id = document.getElementById('id');
var data_key = document.getElementById('data_key');
var data_value = document.getElementById('data_value');
var code = document.getElementById('code');
var instance = document.getElementById('instance');
function createCode() {
    if(var_.value == '') {
        var var_value = 'db';
    }
    else{
        var var_value = var_.value;
    }
    code.innerHTML = `
        $` + var_value + `->setDataFor(array(
            'key'   => '` + key.value + `',
            'value' => '` + id.value + `',
            'data_key'  => '` + data_key.value + `'
            'data_value'  => '` + data_value.value + `'
        ));
    `;
    instance.innerHTML = '$' + var_value;
}
</script>