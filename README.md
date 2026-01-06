CLA.I.RE (Cervical Lab-based Information & Record Evaluation)

CLA.I.RE is a specialized clinical record management system designed for pathologists. It provides a centralized platform for tracking patient demographics and screening events, emphasizing data integrity, security, and professional information management.

🚀 Key Features (Basic CRUD)

Case Management: Complete CRUD (Create, Read, Update, Delete) lifecycle for patient records. Pathologists can register new patients, update demographic details, and archive cases securely.

Pathologist Dashboard: A dynamic activity hub showing real-time statistics (Total, Normal, and Abnormal counts) and a prioritized list of the most recent screening events.

Integrated Analysis Workflow: A "New Analysis" module that maintains persistent patient context, ensuring that clinical findings and images are accurately linked to specific patient profiles.

Search & Filter: Real-time filtering in the Case Management module to quickly locate records by Name, Patient ID, or Case Identifier.

🛠️ Tech Stack

Backend: PHP (PDO) for secure, prepared SQL execution and strict session-based authentication.

Frontend: HTML5, Tailwind CSS, and Phosphor Icons for a responsive, modern clinical interface.

Database: MySQL with a normalized 3NF schema to prevent data redundancy.

Authentication: Secure session handling with BCRYPT password hashing.
