/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
	
		// transfers email of currently selected applicant for mailing by employer
		function transferEmail(email){
		    
		    document.getElementById('email').value=email;
		}
		
		// transfers comma delimited emails of currently selected applicants for mailing by employer
		function transferEmails(t){
		    
		    var c = document.getElementsByTagName('input');
		    emails = "";
		    		    
		    for (var i = 0; i < c.length; i++) {

			if (c[i].type === 'checkbox' && c[i].checked === true && c[i].name!=="parentCheck") {

                          emails += c[i].value+",";
			   
		     }
   
		}
		
		document.getElementById('email').value=emails;
		
		if(emails===""){
			alert("Please Select a Candidate");
			//t.disabled="disabled";
	         }
		  
	      
	    }
	    
	    
	    
		
//	    $(function () { $('#myModal1').modal({
//	    keyboard: true
//	    })});
//	
//	$(function () { $('#myModal2').modal({
//	    keyboard: true
//	    })});
	
	function toggleAllApplicants(t){
	    
	    var c = document.getElementsByTagName('input');
	    for (var i = 0; i < c.length; i++) {
		
		if (c[i].type === 'checkbox') {
		    
		    
		    if(t.checked === false){
		    c[i].checked = false;
		    }
		    else{
		    c[i].checked = true;}
		    }
             }
	}
	
	function onDownloadReady(){
	    
	    var c = document.getElementsByTagName('input');
		    candidateIDs = "";
		    
		    
		    for (var i = 0; i < c.length; i++) {

                    
			if (c[i].type === 'checkbox' && c[i].checked === true && c[i].name!=="parentCheck") {

                          candidateIDs += c[i].name+",";
			   
		     }
   
		   }
		
		document.getElementById('downloadables').value = candidateIDs;
		
		if(candidateIDs===""){
	            alert("Please Select a Candidate");
	            return false;
	        }
		
		return true;
	}
	

      function isCandidateSelected(){
		    
		    if(document.getElementById("email").value===""){ 
			alert("Please select at least one candidate");
	             return false;
		        	
	            }
		    else return true;
		}
		
		//drop down selector for job posting
		 function showOptions(s){
	     
            var selectedOption = s.options[s.selectedIndex];
	    var chosen = selectedOption.value;
           
	   
	   if(chosen==="email"){
	      
	       document.getElementById("by_mail").className = "visible";
	        document.getElementById("by_web").className = "hidden";
	       document.getElementById("in_person").className = "hidden";
	       document.getElementById("by_web").value = "";
	       document.getElementById("by_person").value = "";
	       // document.getElementById("apply_by_mail").style.visibility = "visible";
	       
	   }
	   else if(chosen==="web"){
	       
	        document.getElementById("by_web").className = "visible";
		document.getElementById("by_mail").className = "hidden";
		document.getElementById("in_person").className = "hidden";
		document.getElementById("in_person").value = "";
		document.getElementById("by_mail").value = "";
	   }
	    else if(chosen==="in_person"){
	       
	        document.getElementById("by_web").className ="hidden";
		document.getElementById("by_mail").className = "hidden";
		document.getElementById("in_person").className = "visible";
		document.getElementById("by_mail").value = "";
		document.getElementById("by_web").value = "";
	   }
	   
	   else{
	      document.getElementById("by_web").className = "hidden";
	      document.getElementById("by_mail").className = "hidden";
	      document.getElementById("in_person").className = "hidden";
	   }
	    
        }
	
	
	
    