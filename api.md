home directory 
    http://localhost:80/dashboard/

    (here change the port to what ever the port xampp gives you for testing.)
    (However the final release of the code will have port 80)
    
login api endpoint

    ### User login
    http://localhost:80/dashboard/login/user_login.php

    ### admin login
    http://localhost:80/dashboard/login/admin_login.php

    ##volunteer login
    http://localhost:80/dashboard/login/volunteer_login.php


    method: post
    content-type: application/json
    request-body:   {
                        "submit":"",
                        "email":"",
                        "password":"",
                        "ssid":"xxxxxxxxxxxx" //if exists will be deleted 
                    }

    response: valid request

            http-response-code : 200 (OK)
            {   
                "msg": "valid-user request",
                "ssid": "xxxxxxxxxxxxxxxxxx"  // cookie will be generated
            }
    
    response: invalid user request (unkown email)

            http-response-code: 400 (BAD REQUEST)
            {
                "msg": "invalid-user request"
            }

    response: any field empty

            http-response-code: 400 (BAD REQUEST)
            {
                "msg" : "all field are required"
            }

    


    response: invalid user request (wrong password)
            
            http-response-code: 400 (BAD REQUEST)
            {
                "msg": "wrong password for email"
            }

    response: wrong http-method

            http-response-code: 400 (BAD REQUEST)
            {
                "msg":"invalid request method"
            }



signup api endpoint



    
    ### User sigunup
    http://localhost:80/dashboard/signup/user_signip.php

    ### admin signup
    http://localhost:80/dashboard/signup/admin_signup.php

    ##volunteer signup
    http://localhost:80/dashboard/signup/volunteer_signup.php


    method: post
    content-type: application/json
    request-body:   {
                        "submit":"",  //submit button of a form
                        "email":"",
                        "password":""
                        "name" :""
                    }

    response: valid request

            http-response-code : 200 (OK)
            {   
                "msg": "valid-user request",
                "ssid" : "xxxxxxxxxxxxxxxxxx"
            }
    
    response: invalid user request (already exists email)

            http-response-code: 400 (BAD REQUEST)
            {
                "msg": "email already exists"
            }

    response: any field empty

            http-response-code: 400 (BAD REQUEST)
            {
                "msg" : "all field are required"
            }

    response: wrong http-method

            http-response-code: 400 (BAD REQUEST)
            {
                "msg":"invalid request method"
            }




logout api endpoint  (it deletes the cookie on the browser)

    http://localhost:80/dashboard/logout/logout.php

    method: post
    content-type: application/json
    request-body:   {
                        "submit":"",  //submit button of a form
                        "phpssid":"xxxxxxxxxxxxxxx"  // automatic
                    }

    response: wrong http-method

            http-response-code: 400 (BAD REQUEST)
            {
                "msg":"invalid request method"
            }

    response : correct logout

        http-response-code: 200
        {
            "msg":"logout successful"
        }