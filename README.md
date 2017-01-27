# Jvillage Class Graduation

This CiviCRM extension creates a custom field group containing two custom fields: one in which the user can record the individual's graduating class, and another  which thereby automatically calculates the individual's current school grade.

## Auto-created entities
Upon installation, this extension creates the following entities:

### Custom Field Group: "Grade/Class"
A custom field group attached to all contacts, with two custom fields
* Graduating Class: A text field intended to store a 4-digit year in which the individual is expected to graduate.
* Current Grade: A read-only text field which will contain the individual's current school grade, calculated based on the value of the Graduating Class field.

### Scheduled Job: "Call Classgraduate.Updateall API"
This scheduled job is configured to run daily as part of CiviCRM's Scheduled Job functionality and will update the Current Grade field for all contacts based on the current date and the value of the individual's Graduating Class field.

## Assumptions and limitations
* The extension assumes that students are promoted annually on June 1.  This hard-coded value can be edited in the function `_classgraduate_var_get()`.
* Students are assumed to graduate at the completion of Grade 12.
* The Graduating Class field is a simple text integer field. No checking is done to ensure numbers within a sensible range (e.g., a value of 123456890 is allowed), but if a value leads to a calculated Current Grade that's not between 1 and 12, the Current Grade field will be set empty.

Sponsorship is welcome for efforts to address any of these limitations.