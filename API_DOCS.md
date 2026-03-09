# Getlead HQ — API Documentation

**Base URL:** `http://127.0.0.1:8000`

Method	    URL	                    Auth	           Description
POST	    /api/auth/login	—	    Login →         returns Bearer token (30 days)
POST	    /api/auth/logout	    Bearer	        Invalidate token
GET     	/api/auth/me		    Bearer	        Current user info
GET	        /api/dashboard/my	    Bearer	        Personal stats, streak, calendar
GET	        /api/dashboard/admin	Bearer (admin)	Team overview, activity feed
GET	        /api/tasks		        Bearer	        List with filters
POST	    /api/tasks		        Bearer (admin)	Create (multi-assignee)
GET	        /api/tasks/{id}		    Bearer	        Detail with comments + history
PUT	        /api/tasks/{id}		    Bearer	        Update (non-admin: status only)
DELETE	    /api/tasks/{id}		    Bearer (admin)	Delete task
POST	    /api/tasks/{id}/comment	Bearer	        Add comment
PATCH	    /api/tasks/{id}/status	Bearer	        Quick status change
POST	    /api/reports		    Bearer	        Submit/update daily report
GET	        /api/reports/summary?date=	Bearer (admin)	Summary for a date
GET	        /api/reports/today	    Bearer (admin)	Today's reports (decoded)
GET	        /api/reports/missing	Bearer (admin)	Who hasn't reported
GET	        /api/staff		        Bearer	        Active staff list
GET	        /api/team		        Bearer (admin)	Enriched team list
POST	    /api/team		        Bearer (admin)	Add staff member
PUT	        /api/team/{id}		    Bearer (admin)	Update staff
PATCH	    /api/team/{id}/toggle	Bearer (admin)	Enable/disable

---

## Authentication

** To login user
### POST Requests

curl --location 'http://127.0.0.1:8000/api/auth/login' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data '{
    "mobile":"9048333535",
    "pin":"1234"
}'

## Response :
{
    "ok": true,
    "token": "9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b",
    "staff": {
        "id": 1,
        "name": "Akhil",
        "role": "admin"
    }
}

--------------------------------------
** To logout the user
### POST Requests

curl --location --request POST 'http://127.0.0.1:8000/api/auth/logout' \
--header 'Authorization: Bearer 8|MVIGpTkNCG8rqXrFrE4y2kTu8Bf5aldzEZUF2p5M29cbab22' \
--data ''

## Response:
{
    "ok": true
}

--------------------------------------
** To show user details
### GET 

curl --location 'http://127.0.0.1:8000/api/auth/me' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b'

## Response
{
    "ok": true,
    "staff": {
        "id": 1,
        "name": "Akhil",
        "role": "admin"
    }
}
--------------------------------------
** To get my dashboard
### GET

curl --location 'http://127.0.0.1:8000/api/dashboard/my' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b'

## Response :

{
    "ok": true,
    "tasks_open": 6,
    "tasks_completed_month": 0,
    "tasks_overdue": 4,
    "completion_rate": 14,
    "tasks_completed_week": 0,
    "report_streak": 0,
    "last_report_date": "2026-02-26",
    "last_report_submitted_at": "2026-02-25T23:26:15.000000Z",
    "report_calendar": [
        {
            "date": "2026-02-19",
            "submitted": false
        },
        {
            "date": "2026-02-20",
            "submitted": true
        },
        {
            "date": "2026-02-21",
            "submitted": true
        },
        {
            "date": "2026-02-22",
            "submitted": false
        },
        {
            "date": "2026-02-23",
            "submitted": false
        },
        {
            "date": "2026-02-24",
            "submitted": false
        },
        {
            "date": "2026-02-25",
            "submitted": false
        },
        {
            "date": "2026-02-26",
            "submitted": true
        },
        {
            "date": "2026-02-27",
            "submitted": false
        },
        {
            "date": "2026-02-28",
            "submitted": false
        },
        {
            "date": "2026-03-01",
            "submitted": false
        },
        {
            "date": "2026-03-02",
            "submitted": false
        },
        {
            "date": "2026-03-03",
            "submitted": false
        },
        {
            "date": "2026-03-04",
            "submitted": false
        }
    ],
    "avg_completion_days": 10,
    "reported_today": false
}
--------------------------------------

** to get admin dashboard details
### GET

curl --location 'http://127.0.0.1:8000/api/dashboard/admin' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b'

## Response :

{
    "stats": {
        "total_staff": 18,
        "total_tasks": 102,
        "overdue_tasks": 88,
        "completed_today": 0,
        "reports_submitted": 0,
        "report_rate": 0,
        "week_completion": 0,
        "recent_activity": [
            {
                "id": 153,
                "task_id": 141,
                "staff_id": 1,
                "action": "created",
                "old_value": null,
                "new_value": "this istesting",
                "created_at": "2026-03-04T08:49:23.000000Z",
                "staff_name": "Akhil",
                "task_title": "this istesting"
            },
            {
                "id": 152,
                "task_id": 140,
                "staff_id": 1,
                "action": "created",
                "old_value": null,
                "new_value": "API Test Task",
                "created_at": "2026-03-04T07:24:34.000000Z",
                "staff_name": "Akhil",
                "task_title": "API Test Task"
            },
            ......            
            {
                "id": 143,
                "task_id": 122,
                "staff_id": 9,
                "action": "commented",
                "old_value": null,
                "new_value": "PAYMENT FOLLOW-UP ONGOING",
                "created_at": "2026-02-20T13:46:11.000000Z",
                "staff_name": "Rahoof",
                "task_title": "Shejas payment collection"
            }
        ],
        "team_status": [
            {
                "id": 1,
                "name": "Akhil",
                "role": "admin",
                "pending_tasks": 6,
                "overdue_tasks": 4,
                "last_report": "2026-02-25T18:30:00.000000Z",
                "reported_today": 0
            },
            ......................
            
            {
                "id": 19,
                "name": "Sumisha",
                "role": "support",
                "pending_tasks": 0,
                "overdue_tasks": 0,
                "last_report": null,
                "reported_today": 0
            }
        ],
        "reports_missing": [
            {
                "name": "Akhil",
                "role": "admin"
            },
            {
                "name": "Rakhi",
                "role": "secretary"
            },
            {
                "name": "Harsha",
                "role": "sales_rep"
            },
            ................
            {
                "name": "Sumisha",
                "role": "support"
            }
        ],
        "reports_submitted_list": []
    }
}

--------------------------------------
** To create new task
###POST Requests

curl --location 'http://127.0.0.1:8000/api/tasks' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b' \
--data '{
 "title"       :"this istesting",
 "description" : "this is testing description",
 "assigned_to" : 2,
 "created_by"  : 1,
 "priority"    : "normal",
 "status"      : "pending",
 "category"    :"other",
 "due_date"    : "2026-03-05"
}'

## Response:

{
    "ok": true,
    "count": 1
}

--------------------------------------
** To get all tasks
### GET

curl --location 'http://127.0.0.1:8000/api/tasks' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b'

To show Next page using parameter page=(no). Eg: http://127.0.0.1:8000/api/tasks?page=2

## Response:

{
    "tasks": [
        {
            "id": 44,
            "title": "[Do Verify] Merge PR #3 — Bottom nav fix",
            "description": "Merge and verify bottom navigation works correctly on both platforms",
            "assigned_to": 13,
            "created_by": 1,
            "priority": "urgent",
            "status": "pending",
            "due_date": "2026-02-10",
            "completed_at": null,
            "notes": null,
            "category": "development",
            "created_at": "2026-02-09T11:50:08.000000Z",
            "updated_at": "2026-02-09T11:50:08.000000Z",
            "assignee_name": "Hari",
            "assignee_role": "developer",
            "creator_name": "Akhil"
        },
        {
            "id": 45,
            "title": "[Do Verify] Merge PR #4 — Auth foundation",
            "description": "Merge authentication base code. Verify token handling.",
            "assigned_to": 13,
            "created_by": 1,
            "priority": "urgent",
            "status": "pending",
            "due_date": "2026-02-10",
            "completed_at": null,
            "notes": null,
            "category": "development",
            "created_at": "2026-02-09T11:50:09.000000Z",
            "updated_at": "2026-02-09T11:50:09.000000Z",
            "assignee_name": "Hari",
            "assignee_role": "developer",
            "creator_name": "Akhil"
        },
        ...................

    ],
    "total": 101,
    "page": 1,
    "limit": 20,
    "pages": 6
}

--------------------------------------
** To create new tasks 
### POST

curl --location 'http://127.0.0.1:8000/api/tasks' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b' \
--data '{
 "title"       :"this istesting",
 "description" : "this is testing description",
 "assigned_to" : 2,
 "created_by"  : 1,
 "priority"    : "normal",
 "status"      : "pending",
 "category"    :"other",
 "due_date"    : "2026-03-05"
}'

## Response :
{
    "ok": true,
    "count": 1
}

--------------------------------------
** To get specified task 
### GET 

curl --location 'http://127.0.0.1:8000/api/tasks/125' \
--header 'Accept: application/atom+xml' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b'

## Response :

task not found, return blank array result:
Eg: 
{
    "task": [],
    "comments": [],
    "history": []
}

Other Wise return:

{
    "task": {
        "id": 125,
        "title": "BSNL Bill Payment — ₹1,299 (Monthly 18th)",
        "description": "Recurring monthly payment.\n\nCompany: Getlead Analytics Pvt Ltd\nPhone: 04952994644
                \nAccount: 9040994068\nAmount: ₹1,299/month\nDue: 18th of every month",
        "assigned_to": 9,
        "created_by": 1,
        "priority": "high",
        "status": "pending",
        "due_date": "2026-03-18",
        "completed_at": null,
        "notes": null,
        "category": "finance",
        "created_at": "2026-02-18T08:57:09.000000Z",
        "updated_at": "2026-02-18T08:57:09.000000Z",
        "assignee_name": "Rahoof",
        "assignee_role": null,
        "creator_name": "Akhil"
    },
    "comments": [],
    "history": [
        {
            "id": 130,
            "task_id": 125,
            "staff_id": 1,
            "action": "created",
            "old_value": null,
            "new_value": "BSNL Bill Payment — ₹1,299 (Monthly 18th)",
            "created_at": "2026-02-18T08:57:09.000000Z",
            "staff_name": "Akhil"
        }
    ]
}

--------------------------------------
** To update task Status:
Statuses = ['pending', 'in_progress', 'done', 'blocked']
### PUT

curl --location --request PUT 'http://127.0.0.1:8000/api/tasks/141' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b' \
--data '{
    "status":"in_progress"
}'

## Response :
{
    "ok": true
}

--------------------------------------
** To delete specified task 
### DELETE

curl --location --request DELETE 'http://127.0.0.1:8000/api/tasks/141' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b'

## Response :
{
    "ok": true
}

--------------------------------------
** To add comment to the task
### POST

curl --location 'http://127.0.0.1:8000/api/tasks/140/comment' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b' \
--data '{
    "comment":" This is testing comment"
}'

## Response :
{
    "ok": true
}

--------------------------------------
** To Quick update status
### PATCH

curl --location --request PATCH 'http://127.0.0.1:8000/api/tasks/140/status' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b' \
--data '{
    "status":"in_progress"
}'

## Response :
{
    "ok": true
}

--------------------------------------
** To Submit/update daily report
### POST

curl --location 'http://127.0.0.1:8000/api/reports' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b' \
--data '{
    "data":{
                "tasks":"Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "decisions":"Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "notes":"Lorem Ipsum is simply dummy text of the printing and typesetting industry."
            }
}'

"updated": true -> update existing data
"updated": false -> add new data

## Response :
{
    "ok": true,
    "updated": true
}

--------------------------------------
** To get summary report of the specified date:
### GET

curl --location 'http://127.0.0.1:8000/api/reports/summary?date=2026-02-20' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b'

## Response :

{
    "date": "2026-02-20",
    "reports": [
        {
            "id": 129,
            "name": "Harsha",
            "role": "sales_rep",
            "report_data": "{\"calls_made\":96,\"calls_connected\":62,\"demos_scheduled\":1,
                              \"demos_completed\":1,\"trials\":0,\"payments_closed\":0,\"payments_amount\":0,\"hot_leads\":\"\",\"notes\":\"\"}",
            "submitted_at": "2026-02-20T10:53:55.000000Z",
            "updated_at": null,
            "role_label": "Sales Rep",
            "emoji": "💼",
            "time": "04:23 PM"
        },
       ......................................
       {
            "id": 169,
            "name": "Akhil",
            "role": "admin",
            "report_data": "{\"tasks\":\"hgfhhgfhgfhfgh\",\"decisions\":\"fghhgfhgfhgfhh\",\"notes\":\"hfghghgfhfgh\"}",
            "submitted_at": "2026-02-26T00:09:41.000000Z",
            "updated_at": "2026-02-26T00:10:23.000000Z",
            "role_label": "Admin",
            "emoji": "⚡",
            "time": "05:39 AM"
        }
    ],
    "total_staff": 17,
    "submitted": 11,
    "pending": 6
}

--------------------------------------
** To get Today's Report
### GET

curl --location 'http://127.0.0.1:8000/api/reports/today' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b'

## Response :
{
    "date": "2026-03-04",
    "reports": [
        {
            "id": 174,
            "name": "Akhil",
            "role": "admin",
            "report_data": {
                "tasks": "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "decisions": "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "notes": "Lorem Ipsum is simply dummy text of the printing and typesetting industry."
            },
            "submitted_at": "2026-03-04T10:07:23.000000Z",
            "updated_at": "2026-03-04T10:08:56.000000Z",
            "role_label": "Admin",
            "emoji": "⚡",
            "time": "03:37 PM"
        }
    ],
    "total_staff": 17,
    "submitted": 1,
    "pending": 16
}

--------------------------------------
** To get report for Who hasn't reported
### GET

curl --location 'http://127.0.0.1:8000/api/reports/missing' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b'

## Response :
{
    "date": "2026-03-04",
    "missing": [
        {
            "name": "Rakhi",
            "role": "secretary"
        },
        {
            "name": "Harsha",
            "role": "sales_rep"
        },
        .......................
        {
            "name": "Sumisha",
            "role": "support"
        }
    ],
    "count": 17
}

--------------------------------------
** To get active staff list
### GET

curl --location 'http://127.0.0.1:8000/api/staff' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b'

## Response :

{
    "staff": [
        {
            "id": 1,
            "name": "Akhil",
            "role": "admin",
            "mobile": "9048333535",
            "telegram_id": null,
            "active": 1,
            "role_label": "Admin"
        },
        ..................................
        {
            "id": 19,
            "name": "Sumisha",
            "role": "support",
            "mobile": "7034191814",
            "telegram_id": "",
            "active": 1,
            "role_label": "Support"
        }
    ]
}

--------------------------------------
** To get Enriched team list
### GET
curl --location 'http://127.0.0.1:8000/api/team' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b'

## Response :

{
    "staff": [
        {
            "id": 1,
            "name": "Akhil",
            "role": "admin",
            "mobile": "9048333535",
            "telegram_id": null,
            "active": 1,
            "role_label": "Admin",
            "active_tasks": 5,
            "last_report_date": "2026-03-03T18:30:00.000000Z",
            "last_login": "2026-03-04 13:15:35",
            "initials": "A"
        },
        ............................
        {
            "id": 19,
            "name": "Sumisha",
            "role": "support",
            "mobile": "7034191814",
            "telegram_id": "",
            "active": 1,
            "role_label": "Support",
            "active_tasks": 0,
            "last_report_date": null,
            "last_login": null,
            "initials": "S"
        }
    ]
}

--------------------------------------
** To add staff
### POST

curl --location 'http://127.0.0.1:8000/api/team' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b' \
--data '{
    "name":"qwerty",
    "role":"sales_rep",
    "mobile":"1234567890",
    "telegram_id":null,
    "pin":"1234"
}'

## Response :
{
    "ok": true,
    "id": 20
}

--------------------------------------
** To update staff
### PUT

curl --location --request PUT 'http://127.0.0.1:8000/api/team/20' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b' \
--data '{
    "name":"Qwerty1",
    "role" :"support",
    "mobile":"1234567890",
    "telegram_id":null,
    "pin":"1234"
}'

## Response :
{
    "ok": true
}

--------------------------------------

** To toggle user status (Active/Inactive)
### PATCH

curl --location --request PATCH 'http://127.0.0.1:8000/api/team/20/toggle' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 9|G9VxnFFViYOOe074RlPk8WijMPTCXGZqHxUBqVvN4c495c3b' \
--data ''

## Response :

{
    "ok": true,
    "active": true
}

--------------------------------------
