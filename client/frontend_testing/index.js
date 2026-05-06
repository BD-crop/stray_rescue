

(async function(){
    
    await fetch("http://localhost:80/dashboard/profile/profile.php", {
            method: 'GET',
    }).then(res => res.json()).then(res =>{
        let obj= res;

        if(obj['msg']==='found'){
            profile_name.innerText = obj['id'];
            type1.innerText = obj['type'];
            profile_image.src=obj['image'];
            console.log(obj);
        }


    });
})();


