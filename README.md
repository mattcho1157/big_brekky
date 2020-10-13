# BIG BREKKY ONLINE MANAGEMENT SYSTEM

@ Copyright 2020 Matthew Mingyeom Cho (more details in LICENSE file)

Browse the "bigbrekky" folder above to view my code for this website.

## CONTEXT
Big Brekky is a social service program at my school - St Joseph’s College, Gregory Terrace - where teams of students and organisers cook burger breakfasts for homeless people every weekday, while sharing genuine conversations. The school’s Ministry team requested me to program a web-enabled data-driven digital solution to replace their existing pen and paper management system. The Big Brekky online management system accommodates three key user types: students, organisers and the admin. The features and functions presented to each user type varies to meet their specific needs for participating and managing the Big Brekky program. 

## USER TYPES
All users will be able to register an account and log in. A 2-factor authentication process will be implemented via account verification emails.
### Student actions
- Select weekday preferences for Big Brekky enrolment and view selected preferences
- Needs the ability to edit preferences
- Download electronic copy of the parental permission form
- Upload a digital copy of their completed parental permission form securely
- Report issues encountered during Big Brekky sessions (anonymously if need be)
### Organiser actions
- View event summaries – include date, type and location, quota and current registration levels of participants
- Apply participation caps or encourage more to get involved
- View and manage students’ parental permission form
- Print out an attendance roll and complete it online later when a digital device is accessible
- Annotate student profiles with performance notes
- Edit every aspect of the roster, including event details and the students & organisers allocated to each event. Organisers should also have the ability to add new events.
### Admin actions
- Upload user data and promote users to organisers
- Generate semester participation reports for inclusion in semester reporting

## GOALS
### Data requirements
- Admin can add cohort studentbase data by uploading an appropriate .csv file. This dataset comprises: s-number, first & last names, email address, house & PC group
- Rostered sessions have a minimum of: date, street address, start and end times, attendance cap, minimum required no. of organisers
### User experience
- Comply with Australian Accessibility Standards and the College Style Guidelines booklet
- Comply with the Australian Privacy Act (1998) – ensure that only authorised personnel can view personal data and inform users of their data being stored
- Include appropriate attribution to data and images used. Must comply with copyright law
- Sanitise user input to prevent SQL injection attacks and securely store student information and passwords. Implement 2-factor authentication for secure registration
### Others
- Automatically generate a recommended term roster based on student preferences to reduce the workload of the organisers’ when managing the roster
- Implement AJAX XMLHttpRequests for live database queries and webpage content updates without having to reload the page
- UI is compatible with mobile platforms (responsive) and appealing to the human eye
- Generation of efficient programming components, data elements and user interface
- Error-proofing mechanisms to prevent defects caused by users
- Compliant with useability principles (effectiveness, accessibility, safety)
- Consider personal, social and economic implications to identify risks of the Big Brekky digital system

## IMPACTS
### Personal impacts
This digital Big Brekky system will provide students with a mechanism for conveniently submitting their weekday preferences online directly to the Big Brekky management team. They will also be able to instantly contact the admin if they desire to request for a resubmission. However, such ease comes with a price. Student’s privacy and security of personal data is jeopardised by registering their accounts, as this website is still at its early stages of development; thus, minimal security measures have been implemented. Furthermore, the digital record of student profiles and rolls will serve as a secondary source of information for organisers, in case they lose their paper copies. As well, by being able to instantly analyse any student’s annotations at any time via the web, organisers will be able to quickly derive methods for enhancing pupil’s experiences.
### Social impacts
There are no major social impacts as this website will not be implemented for use by the wider general public, instead merely within Terrace.
### Economic impacts
If the school implements this digital solution, they will not have to waste money in hiring a separate web-development company to build and maintain the system. The digital solution is designed in a way such that only one administrator is required for managing the system, hence requiring minimal expenses for Terrace as the employee. By transferring a paper-spreadsheet system into an entirely digital online application, less money will be wasted on printing. As well, this digital system will allow highly efficient management of the Big Brekky program to spend more time on ameliorating student experiences – time is money!
### Legal (digital copyright) impacts
Permission to store students’ personal data has been granted by their guardians (already being stored on databases like TASS). No images were used other than those retrieved from the official Terrace website. Therefore, digital copyright issues with the application are not anticipated.

## RECOMMENDATIONS FOR V2.0
There are further significant improvements to be made. Firstly, each page has to download a set of google fonts that were used as an alternative to pre-installed fonts for a modern design. This hindered the efficiency of the website, especially when the Wi-Fi connection was moderately slow. This could be overcome by importing only the required font families and sizes, which will substantially increase efficiency. As well, the homepage features a massive banner image that is over 3000 pixels wide to support desktop screens, which can also slow down the website’s speed. This can be resolved by utilising CSS to scale images depending on the viewport size of the screen. In general, more time must be spent on cleaning up the code to reduce redundancy (e.g. unnecessarily repeated code), which could not be done due to time constraints.

Despite several security measures hashing passwords, sanitising text inputs, and using POST method for sending forms, this website is vulnerable to more significant and deleterious cyber-attacks such as malware, phishing schemes and stolen data as I, the developer, should prevent by implementing mechanisms to impede network breaches. This will increase privacy and security of extremely sensitive information such as students’ personal data, which could potentially be stolen.

As well, the future version of this website must use HTTPS protocol, which further prevents hackers from changing information on the page to accumulate personal information from users. Protection against XSS (cross-site scripting) attacks must also be considered for security against malicious injected JavaScript code that could alter page content or send vulnerable information to the attacker. Furthermore, if the Big Brekky system was to be extended to be fully operational, components such as the report-issue form for students should be programmed to be functional. Such improvements will allow an entirely digital system for Big Brekky management at Terrace.
