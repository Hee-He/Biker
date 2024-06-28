-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: bikerental
-- ------------------------------------------------------
-- Server version	8.0.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'admin','5c428d8875d2948607f3e3fe134d71b4','2017-06-18 12:22:38');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblbooking`
--

DROP TABLE IF EXISTS `tblbooking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblbooking` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userEmail` varchar(100) DEFAULT NULL,
  `VehicleId` int DEFAULT NULL,
  `FromDate` varchar(20) DEFAULT NULL,
  `ToDate` varchar(20) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `Status` int DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblbooking`
--

LOCK TABLES `tblbooking` WRITE;
/*!40000 ALTER TABLE `tblbooking` DISABLE KEYS */;
INSERT INTO `tblbooking` VALUES (1,'test@gmail.com',2,'22/06/2017','25/06/2017','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco',2,'2017-06-19 20:15:43'),(2,'test@gmail.com',3,'30/06/2017','02/07/2017','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco',2,'2017-06-26 20:15:43'),(3,'test@gmail.com',4,'02/07/2017','07/07/2017','Lorem ipsumLorem ipsumLorem ipsumLorem ipsumLorem ipsumLorem ipsumLorem ipsumLorem ipsumLorem ',1,'2017-06-26 21:10:06'),(4,'test@gmail.com',1,'12/02/2024','12/06/2024','i love this bike',0,'2024-05-22 09:31:03');
/*!40000 ALTER TABLE `tblbooking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblbrands`
--

DROP TABLE IF EXISTS `tblbrands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblbrands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `BrandName` varchar(120) NOT NULL,
  `CreationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblbrands`
--

LOCK TABLES `tblbrands` WRITE;
/*!40000 ALTER TABLE `tblbrands` DISABLE KEYS */;
INSERT INTO `tblbrands` VALUES (1,'KTM','2017-06-18 16:24:34','2017-06-19 06:42:23'),(2,'Bajaj','2017-06-18 16:24:50',NULL),(3,'Honda','2017-06-18 16:25:03',NULL),(4,'Suzuki','2017-06-18 16:25:13',NULL),(5,'Yamaha','2017-06-18 16:25:24',NULL),(7,'Ducati','2017-06-19 06:22:13',NULL);
/*!40000 ALTER TABLE `tblbrands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblcontactusinfo`
--

DROP TABLE IF EXISTS `tblcontactusinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblcontactusinfo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Address` tinytext,
  `EmailId` varchar(255) DEFAULT NULL,
  `ContactNo` char(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblcontactusinfo`
--

LOCK TABLES `tblcontactusinfo` WRITE;
/*!40000 ALTER TABLE `tblcontactusinfo` DISABLE KEYS */;
INSERT INTO `tblcontactusinfo` VALUES (1,'Test Demo test demo																									','test@test.com','8585233222');
/*!40000 ALTER TABLE `tblcontactusinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblcontactusquery`
--

DROP TABLE IF EXISTS `tblcontactusquery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblcontactusquery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `EmailId` varchar(120) DEFAULT NULL,
  `ContactNumber` char(11) DEFAULT NULL,
  `Message` longtext,
  `PostingDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblcontactusquery`
--

LOCK TABLES `tblcontactusquery` WRITE;
/*!40000 ALTER TABLE `tblcontactusquery` DISABLE KEYS */;
INSERT INTO `tblcontactusquery` VALUES (1,'Harry Den','webhostingamigo@gmail.com','2147483647','Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum','2017-06-18 10:03:07',1),(2,'rabin','asd@gmail.com','9840430486','asdasdas','2024-06-14 15:05:30',NULL);
/*!40000 ALTER TABLE `tblcontactusquery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblpages`
--

DROP TABLE IF EXISTS `tblpages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblpages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `PageName` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `detail` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblpages`
--

LOCK TABLES `tblpages` WRITE;
/*!40000 ALTER TABLE `tblpages` DISABLE KEYS */;
INSERT INTO `tblpages` VALUES (1,'Terms and Conditions','terms','<P align=justify><FONT size=2><STRONG><FONT color=#990000>(1) ACCEPTANCE OF TERMS</FONT><BR><BR></STRONG>Last updated: December 05, 2017\nPlease read these Terms and Conditions (\"Terms\", \"Terms and Conditions\") carefully before using the ->code-projects.org/ website (the \"Service\") operated by Code Projects (\"us\", \"we\", or \"our\").\nYour access to and use of the Service is conditioned on your acceptance of and compliance with these Terms. These Terms apply to all visitors, users and others who access or use the Service.\nBy accessing or using the Service you agree to be bound by these Terms. If you disagree with any part of the terms then you may not access the Service. Terms and Conditions from TermsFeed for Code Projects. Links To Other Web Sites\nOur Service may contain links to third-party web sites or services that are not owned or controlled by Code Projects.\nCode Projects has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third party web sites or services. You further acknowledge and agree that Code Projects shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with use of or reliance on any such content, goods or services available on or through any such web sites or services.\nWe strongly advise you to read the terms and conditions and privacy policies of any third-party web sites or services that you visit. Governing Law\nThese Terms shall be governed and construed in accordance with the laws of New York, United States, without regard to its conflict of law provisions.\nOur failure to enforce any right or provision of these Terms will not be considered a waiver of those rights. If any provision of these Terms is held to be invalid or unenforceable by a court, the remaining provisions of these Terms will remain in effect. These Terms constitute the entire agreement between us regarding our Service, and supersede and replace any prior agreements we might have between us regarding the Service. Changes\nWe reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material we will try to provide at least 30 days notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.\nBy continuing to access or use our Service after those revisions become effective, you agree to be bound by the revised terms. If you do not agree to the new terms, please stop using the Service. Contact Us\nIf you have any questions about these Terms, please contact us. </FONT></P>'),(2,'Privacy Policy','privacy','<span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; text-align: justify;\">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat</span>'),(3,'About Us ','aboutus','<span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; text-align: justify;\">WE ARE THE BIKE RENTAL MANAGER. The only 100% dedicated bike rental booking website. The first Bike Rental Manager (BRM) shop was our own bike shop. Ever Since it has been our aim to make bike rental easier for everyone, everywhere.We focus on making bike rentals easier for you.Your rental business has a unique set of challenges. We are the only dedicated bike rental site and will be able to offer you a solution to match your needs.Get in touch with us today! We provide affordable bike rates, we hae lost of Satiisfied customers feedback, you can have a look at our home page too!! </span>'),(11,'FAQs','faqs','																														<span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; text-align: justify;\">How do I use discounts coupons?\nExcept for promotion codes, Our discounts are applied automatically if your reservation meets any of the criteria mentioned above.\n\nTo use a promotion code, simply enter the code on the homepage widget as you start your reservation. You can do this by selecting the \"Have a promotion code?\" prompt. Promotion codes can also be entered on the checkout page, under Reservation Total. Please note that the promotion code prompt does not appear for certain types of reservations, such as reservations made on the Deals page.\n<br>\nOur Discounts Terms & Conditions\nWe no longer offer or accept returning customer discounts. All discounts are non-transferable and cannot be combined with additional promotions or discounts.</br>\n\n* Liability in case accident:\nThe hirer should have coverage through his own accident and liability insurance.\nThe renting company is not responsible under any circumstances for accidents or damages caused to the hirer or which the hirer causes to any third party or cases of liability </span>');
/*!40000 ALTER TABLE `tblpages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblusers`
--

DROP TABLE IF EXISTS `tblusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblusers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `FullName` varchar(120) DEFAULT NULL,
  `EmailId` varchar(100) DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `ContactNo` char(11) DEFAULT NULL,
  `dob` varchar(100) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `Country` varchar(100) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblusers`
--

LOCK TABLES `tblusers` WRITE;
/*!40000 ALTER TABLE `tblusers` DISABLE KEYS */;
INSERT INTO `tblusers` VALUES (5,'rabin','asd@gmail.com','202cb962ac59075b964b07152d234b70','9840430486',NULL,NULL,NULL,NULL,'2024-06-14 15:02:38',NULL);
/*!40000 ALTER TABLE `tblusers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblvehicles`
--

DROP TABLE IF EXISTS `tblvehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblvehicles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `VehiclesTitle` varchar(150) DEFAULT NULL,
  `VehiclesBrand` int DEFAULT NULL,
  `VehiclesOverview` longtext,
  `PricePerDay` int DEFAULT NULL,
  `FuelType` varchar(100) DEFAULT NULL,
  `ModelYear` int DEFAULT NULL,
  `SeatingCapacity` int DEFAULT NULL,
  `Vimage1` varchar(120) DEFAULT NULL,
  `Vimage2` varchar(120) DEFAULT NULL,
  `Vimage3` varchar(120) DEFAULT NULL,
  `Vimage4` varchar(120) DEFAULT NULL,
  `Vimage5` varchar(120) DEFAULT NULL,
  `AirConditioner` int DEFAULT NULL,
  `PowerDoorLocks` int DEFAULT NULL,
  `AntiLockBrakingSystem` int DEFAULT NULL,
  `BrakeAssist` int DEFAULT NULL,
  `PowerSteering` int DEFAULT NULL,
  `DriverAirbag` int DEFAULT NULL,
  `PassengerAirbag` int DEFAULT NULL,
  `PowerWindows` int DEFAULT NULL,
  `CDPlayer` int DEFAULT NULL,
  `CentralLocking` int DEFAULT NULL,
  `CrashSensor` int DEFAULT NULL,
  `LeatherSeats` int DEFAULT NULL,
  `RegDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblvehicles`
--

LOCK TABLES `tblvehicles` WRITE;
/*!40000 ALTER TABLE `tblvehicles` DISABLE KEYS */;
INSERT INTO `tblvehicles` VALUES (1,'SS400',2,'Slowly spreading its cards this year, the Ace of Bajaj Autos is still not on the table. With the expectations like Pulsar 400SS or Pulsar SS400, the Ace (400SS) would be the commander of the Pulsar series. It would be a benchmark for the other motorcycle manufacturers as the competition for more performance oriented bikes will definitely rise this year.',5000,'Petrol',3453,2,'knowledges_base_bg.jpg','20170523_145633.jpg','codepro.png','social-icons.png','',1,1,1,1,1,1,1,1,1,1,1,1,'2017-06-19 11:46:23','2024-06-14 14:58:35'),(2,'RS200',2,'The Pulsar is by far the most successful brand Bajaj has managed to create in the recent past.It is also fast, no doubt. But, its true highlight is its easy to ride nature. ',4500,'Petrol',2015,2,'bike_755x430.png','looking-used-bike.png','front-image.jpg','about_services_faq_bg.jpg','',1,1,1,1,1,1,1,NULL,1,1,NULL,NULL,'2017-06-19 16:16:17','2024-06-14 14:58:35'),(3,'R1',4,' The Suzuki GSX-R1000 is a sport bike from Suzuki GSX-R series of motorcycles.It was introduced in 2001 to replace the GSX-R1100 and is powered by a liquid-cooled 999 cc (61.0 cu in) inline four-cylinder, four-stroke engine.',4200,'Petrol',2012,2,'featured-img-300.jpg','dealer-logos.jpg','img_390x3900.jpg','listing_img303.jpg','',1,1,1,1,1,1,NULL,1,1,NULL,NULL,NULL,'2017-06-19 16:18:20','2024-06-14 14:58:35'),(4,'Duke390',1,'The KTM 390 DUKE breathes life into values that have made motorcycling so amazing for decades. It combines maximum riding pleasure with optimum user value and comes out on top wherever nimble handling counts. Light as a feather, powerful and packed with state-of-the-art technology, it guarantees a thrilling ride, whether youre in the urban jungle or a forest of bends. 390 DUKE – nowhere you will find more motorcycle per euro.',5600,'Petrol',2012,2,'ktm1.jpg','ktm2.jpg','ktm3.jpg','ktm4.jpg','ktm5.jpg',1,1,1,1,1,1,1,1,1,NULL,NULL,NULL,'2017-06-19 16:18:43','2024-06-15 08:32:47'),(5,'R1',5,'The YZF-R1® features a lightweight and compact crossplane crankshaft, inline-four-cylinder, 998cc high output engine. Featuring titanium fracture-split connecting rods, an offset cylinder block and magnesium covers, the motor delivers extremely high horsepower and a strong pulse of linear torque for outstanding performance, all wrapped in aerodynamic MotoGP®-style bodywork.',6000,'Petrol',3453,2,'bikes_755x430.png',NULL,NULL,NULL,NULL,1,1,1,1,1,1,1,1,1,1,1,1,'2017-06-20 17:57:09','2024-06-14 14:58:35');
/*!40000 ALTER TABLE `tblvehicles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-06-15 17:14:19
