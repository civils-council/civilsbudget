#API DOC

## Projects

### List

__GET__ `/api/projects`
        `?clid={clid}`

####Response

__200__

    {
        "projects": [{
            "vote": "true/false"
            "id": 1,
            "title": "test title",
            "description": "test description",
            "charge": "charge for project",            
            "source": "where",
            "picture": "<url-picture>",
            "createdAt": "2015-10-03T09:11:04+00:00",
            "likes_count": 123,
            "likes_user": user[],
            "owner": "fullName",
            "avatar_owner": "avatar owner"
        }]
    }

### One project

__GET__ `/api/projects/{id}`
        `?clid={clid}`

###Response

__200__

    {
        "project": {
            "vote": "true/false"
            "id": 1,
            "title": "test title",
            "description": "test description",
            "charge": "charge for project",            
            "source": "where",
            "picture": "<url-picture>",
            "createdAt": "2015-10-03T09:11:04+00:00",
            "likes_count": 123,
            "owner": "fullName",
            "avatar_owner": "avatar owner"            
        }
    }

### Like project

__header__ `X-API-KEY`
__POST__ `/api/projects/{id}/like` 
          `{id} - id project`
          `{clid} - uniq clid code request->parameters-get{'clid'}`                                          

__200__

    {
        {
          "warning": "Ви вже підтримали цей проект."
        }
        
        {
          "success": "Ваший голос зараховано на підтримку проект."
        }
        
        {
          'warning', "Ви використали свiй голос."
        }                        
    }
    
__GET__ `/api/projects/{id}/like`

    {
        "project": {
            "id": 1,
            "title": "test title",
            "description": "test description",
            "charge": "charge for project",
            "source": "where",
            "picture": "<url-picture>",
            "createdAt": "2015-10-03T09:11:04+00:00",
            "likes_count": 123,
            "likes_user": user
            [
                    "user": {
                        "id": 111
                        "fullName": <fullName of user>,
                        "clid": <very secret key>,
                    }
            ],
            "owner": "fullName",
            "avatar_owner": "avatar owner"            
        }
    }


#### Request

#### Response

__200__ 

    {
        "project": {
            "id": 1,
            "title": "test title",
            "description": "test description",
            "charge": "charge for project",
            "source": "where",
            "picture": "<url-picture>",
            "createdAt": "2015-10-03T09:11:04+00:00",
            "likes_count": 123,
            "likes_user": user
            [
                    "user": {
                        "id": 111
                        "fullName": <fullName of user>,
                        "clid": <very secret key>,
                    }
            ],
            "owner": "fullName",
            "avatar_owner": "avatar owner"   
        }
    }

## User

### Authorization user

__GET__ `/api/authorization?code=idSlnv44178`

__POST__ `/api/authorization`

#### Request

    {
        "code": <bankId code>
    }

#### Response

__200__

    {
        "user": {
            "id": 111
            "fullName": <fullName of user>,
            "clid": <very secret key>,
            "voted_project": <id project>/false
        }
    }

__401__

    {
        "error": {
            "code": 401,
            "message": "Wrong authorization."
        }
    }
    
### Check bi_oauth_url

__GET__ `/api/settings`    
