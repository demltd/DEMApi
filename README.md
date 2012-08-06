dem-api
=======

PHP API library for managing institution data with Digital Education Marketing (DEM)

Course

GET v1/course
    pid (optional)  restricts courses to the provider with matching id

GET v1/course/1

PUT v1/course/1
    active (optional)
    title (optional)
    subject_areas (optional)
    contact_name (optional)
    contact_position (optional)
    contact_telephone (optional)
    contact_fax (optional)
    contact_email (optional)
    info_url (optional)

POST v1/course
    pid (required)                  assigns the course to the provider with matching id
    active (required)               sets the course as active or inactive
    title (required)                sets the course title
    subject_areas (optional)
    contact_name (optional)
    contact_position (optional)
    contact_telephone (optional)
    contact_fax (optional)
    contact_email (optional)
    info_url (optional)

DELETE v1/course/1

Variation

GET v1/variation
    cid (required)  restricts the variations returned to the ones belonging to
                    the course with the matching id.

PUT v1/variation/1
    tuition_types (optional)
    award_types (optional)
    mode_of_study (optional)
    duration_unit (optional)