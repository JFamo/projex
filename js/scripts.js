//Function to change between joining an existing organization and creating a new one in registration form
function changeOrganizationAction(){
	var orgActionSelect = document.getElementById("organization-action");
	var orgNameForm = document.getElementById("organizationNameForm");
	var orgCodeForm = document.getElementById("organizationCodeForm");

	if(orgActionSelect.options[orgActionSelect.selectedIndex].value == "join"){
		orgCodeForm.style.display = "block";
		orgNameForm.style.display = "none";
	}
	if(orgActionSelect.options[orgActionSelect.selectedIndex].value == "create"){
		orgCodeForm.style.display = "none";
		orgNameForm.style.display = "block";
	}
}

function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    document.getElementById("pageBody").style.marginLeft = "250px";
    document.getElementById("openNavButton").style.paddingLeft = "250px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0px";
    document.getElementById("pageBody").style.marginLeft = "0";
    document.getElementById("openNavButton").style.paddingLeft = "0";
}

function toggleNav(){
  if(document.getElementById("mySidenav").style.width == "0px"){
    openNav();
  }
  else{
    closeNav();
  }
}

//enable popovers
$(document).ready(function(){
    $('[data-toggle="popover"]').popover({container: 'body'});   
});