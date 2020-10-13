create table studentbase (
	snumber varchar(10) not null,
	lname varchar(30) not null,
	fname varchar(30) not null,
	ylevel integer unsigned not null,
	pc varchar(2) not null,
	house varchar(30) not null,
	email varchar(50) not null,
	primary key(snumber)); 

create table users (
	username varchar(30) not null,
	email varchar(50) not null,
	pwd varchar(40) not null,
	fname varchar(30) not null,
	lname varchar(30) not null,
	usertype varchar(1) not null,
	verified boolean not null,
	token varchar(40),
	primary key(username)); 

create table students (
	username varchar(30) not null,
	house varchar(30) not null,
	pc varchar(2) not null,
	ylevel integer unsigned not null,
	annotations varchar(500),
	permitted boolean not null,
	formfilepath varchar(100) not null,
	resubmitpref boolean not null,
	primary key(username),
	foreign key(username) references users(username));

create table preferences (
	username varchar(30) not null,
	prefnum integer unsigned not null,
	week varchar(1) not null,
	day varchar(3) not null,
	primary key(username, prefnum),
	foreign key(username) references students(username)); 

create table events (
	eventid integer unsigned auto_increment not null,
	eventdate date not null,
	week integer unsigned not null,
	day varchar(3) not null,
	starttime time not null,
	endtime time not null,
	place varchar(80) not null,
	studentquota integer unsigned not null,
	organiserquota integer unsigned not null, 
	primary key(eventid)); 

create table studentevents (
	username varchar(30) not null,
	eventid integer unsigned not null,
	primary key(username, eventid),
	foreign key(username) references students(username),
	foreign key(eventid) references events(eventid)); 

create table organiserevents (
	username varchar(30) not null,
	eventid integer unsigned not null,
	primary key(username, eventid),
	foreign key(username) references users(username),
	foreign key(eventid) references events(eventid)); 

create table issues (
	issueid integer unsigned auto_increment not null,
	username varchar(30),
	eventid integer unsigned not null,
	issue varchar(500) not null,
	primary key(issueid),
	foreign key(username) references students(username),
	foreign key(eventid) references events(eventid));

create table reports (
	reportid integer unsigned auto_increment not null,
	eventid integer unsigned not null,
	servings integer unsigned not null,
	feedback varchar(500),
	primary key(reportid),
	foreign key(eventid) references events(eventid));

create table absences (
	username varchar(30) not null,
	reportid integer unsigned not null,
	primary key(username, reportid),
	foreign key(username) references students(username),
	foreign key(reportid) references dailyreports(reportid)); 

create table termweeks (
	term integer unsigned not null,
	weeknum integer unsigned not null,
	startdate date not null
	primary key(term));