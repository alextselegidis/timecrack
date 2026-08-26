# Release Notes

## [Unreleased]

### Added

- Install Timecrack as an app (PWA) with a web manifest, app icons, a service worker and an offline page
- Show tables as one card per row on phones, with the column names as labels
- Show the latest blog posts of timecrack.org on the about page, cached for a day
- Pause and resume the running timer, so that breaks stay in the duration but count as non-billable time
- Let every user pick a timezone in the account page, which the trackings, the filters, the CSV export and
  the manual entry forms are displayed and entered in

### Changed

- Refresh the look of the application with a new set of design tokens (typography, colors, radii and shadows)
- Replace the gray secondary elements with dark ones, so the interface only combines cinnamon orange and dark
- Darker borders, muted text and outlined buttons for more contrast, and a white background for every input
- Keep every table row on a single line on larger screens, so the times are always readable, and scroll
  wide tables inside their card instead of stretching the page
- Larger touch targets, sticky active timer and safe area padding on mobile devices
- Allow pinch to zoom on mobile devices again
- Derive the session and "remember me" cookie names from `APP_KEY` instead of `APP_URL`, so that two
  installations on the same domain can no longer log each other out
- Move the shared markup of the page head into a single `shared.head` view

### Fixed

- Fix billable hours calculation for short duration trackings and update related UI logic
- Keep the "remember me" lifetime within the 400 days that browsers accept for a persistent cookie
- Fix the test suite, which was failing on a missing database and on tests for routes that no longer exist
- Highlight the copied row of the history table on striped rows as well


## [1.0.0] - 2026-03-26

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
