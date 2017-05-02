use tenniesjd13;
drop table if exists locations;
create table locations (
        id int not null primary key auto_increment,
        name varchar(50),
        description varchar(128),
       	latitude double(9,6),
        longitude double(9,6),
	zoomLevel integer(2)
        );

        insert into locations values (null, 'Joshua Tennies', 'NYC', 40.758900, -73.985100, 18);
	insert into locations values (null, 'Joshua Tennies', 'Taj Mahal', 27.175000, 78.042200, 18);
        insert into locations values (null, 'Joshua Tennies', 'Batman Symbol', 26.357896, 127.783809, 18);
        insert into locations values (null, 'Joshua Tennies', 'Guitar-Shaped Forest', -33.867886, -63.987000, 16);
        insert into locations values (null, 'Joshua Tennies', 'Giant Target Shape', 37.563936, -116.851230, 17);

     
