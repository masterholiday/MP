function checkAllIven(n){
	for(var i=1; i<=n; i++){
		$("#iven"+i).attr('checked', true);
	}
}

function checkAllUser(n){
	for(var i=1; i<=n; i++){
		$("#user"+i).attr('checked', true);
	}
}

function checkAll(n,k){
	for(var i=1; i<=n; i++){
		$("#iven"+i).attr('checked', true);
	}
	for(var i=1; i<=k; i++){
		$("#user"+i).attr('checked', true);
	}
}