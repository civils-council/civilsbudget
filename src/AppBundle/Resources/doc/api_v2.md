# API DOC

## Votings

### List

__GET__ `/api/votings`


#### Response

__200__

    {
        "votings": [
            {
                "id": 1,                                            (required)
                "status": "active",                                 (required, {"archived" | "active" | "future"})            
                "title": "string title",                            (required)
                "description": "string description",                (optional)
                "location": "Cherkasy",                             (required)    
                "max_votes_count": 4,                               (required)
                "date_from": "2017-09-23T18:14:15+00:00",           (required)
                "date_to": "2017-09-25T18:14:15+00:00",             (required)
                "logo": "http://imisto.com.ua/img/logo.png",        (optional)
                "background_image": "http://imisto.com.ua/bg.png"   (optional)
            }
        ]
    }


## Projects

### List

__GET__ `/api/votings/{voting_id}/projects`

#### Response

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

__GET__ `/api/votings/{voting_id}/projects/{project_id}`
        `?clid={clid}`

### Response

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

## Auth Settings 

__GET__ `/api/settings`    

### Response

__200__

    {
        "bi_auth_url": "https:\/\/bankid.org.ua",
        "bi_client_id": "string client id,
        "bi_redirect_uri": "https:\/\/vote.imisto.com.ua\/api\/login"
    }

## Like project

### Vote

__header__ `X-API-KEY`
__POST__ `/api/votings/{voting_id}/projects/{project_id}/like`
          `{voting_id}  - voting id`
          `{project_id} - id project`
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
    
__GET__ `/api/votings/{voting_id}/projects/{project_id}/like`

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
