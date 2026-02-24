# Release Notes

## [Unreleased]

### Added

- Add history page for users to view their past activities and changes in the application
- Add dashboard page for users to view an overview of their projects, tasks, and other relevant information
- Allow users to pin a project in dashboard for quick access
- Random color selection for new projects (can be overridden by the user when creating or editing a project)
- Add a "billable" field for each tracking  
- Add non-billable column to the history table

### Changed

- Change the structure of the application for better user experience
- Update to latest Laravel version and update the dependencies to their latest versions
- Replace use of UUID with simple ID for better performance and simplicity
- Replace use of SCSS with simple CSS based on Bootstrap
- Remove the pause functionality from the app 
- Updates to the layout and functionality of the dashboard


## [0.2.0] - 2023-07-20

### Added

- Allow the admin to assign a project to users, from the project create and edit pages (#18)

### Changed

- Disable the autocomplete in the email & password fields of the user form (#16)

### Fixed

- Do not display recorded tasks of other users to non-admin users when visiting the tasks table (#15)
- Display a "No records found" row if the table of any app page is empty (#17)


## [0.1.0] - 2023-07-12

### Added

- Add other common pages such as user dashboard, about, settings and other account related pages (#12)
- Allow the admin to manage multiple projects in the application (#5)
- Allow the admin to manage multiple users in the application (#4)
- Set up the application with Laravel Breeze and prepare the initial structure (#1)
