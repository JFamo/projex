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