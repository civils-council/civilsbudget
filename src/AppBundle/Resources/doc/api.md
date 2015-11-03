#API DOC

## Projects

### List

__GET__ `/api/projects`

####Response

__200__

    {
        "projects": [{
            "id": 1,
            "title": "test title",
            "description": "test description",
            "source": "where",
            "picture": "<url-picture>",
            "createdAt": "2015-10-03T09:11:04+00:00",
            "likes": 123,
            "owner": "fullName"
        }]
    }

### One project

__GET__ `/api/projects/{id}`

###Response

__200__

    {
        "project": {
            "id": 1,
            "title": "test title",
            "description": "test description",
            "source": "where",
            "picture": "<url-picture>",
            "createdAt": "2015-10-03T09:11:04+00:00",
            "likes": 123,
            "owner": "fullName"
        }
    }

### Like project

__header__ `X-API-KEY`
__POST__ `/api/projects/{id}/like`

#### Request

#### Response

__200__ 

    {
        "project": {
            "id": 1,
            "title": "test title",
            "description": "test description",
            "source": "where",
            "picture": "<url-picture>",
            "createdAt": "2015-10-03T09:11:04+00:00",
            "likes": 123,
            "owner": "fullName"
        }
    }

## User

### Authorization user

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
            "apiKey": <very secret key>,
            "apiKeyExpired": "2015-10-03T09:11:04+00:00"
        }
    }

__401__

    {
        "error": {
            "code": 401,
            "message": "Wrong authorization."
        }
    }
