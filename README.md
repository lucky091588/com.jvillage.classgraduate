# Jvillage Class Graduation

This CiviCRM extension creates a custom field group containing two custom fields: one in which the user can record the contact's graduating class, and another  which thereby automatically calculates the contact's current school grade.

## Auto-created entities
Upon installation, this extension creates the following entities:

### Custom Field Group: "Grade/Class"
A custom field group attached to all contacts, with two custom fields
* Graduating Class: A text field intended to store a 4-digit year in which the contact is expected to graduate.
* Current Grade: A read-only text field which will contain the contact's current school grade, calculated based on the value of the Graduating Class field.

### Scheduled Job: "Call Classgraduate.Updateall API"
This scheduled job is configured to run daily as part of CiviCRM's Scheduled Job functionality and will update the Current Grade field for all contacts based on the current date and the value of the contact's Graduating Class field.

