	UserListAPI
	�����: freecod@mail.ru
	
	���������� API ������� � ���������� � �������������
	
	�������� ������� (�� � �������)
	---
	����������� ���������� � API ��� ������� � ���������������� ������ ����� ���. ������
	������������� �������� � ��. � ������� ������������ ���� nick, login � email.
	����������������
	����������: 1. �����������
	� ��������� ������ ������������� �� �������� (nick, login, email), GET������
	� ��������� ������������ �� id, GET������
	� ���������� ������������ (��������� ����, email), POST������
	� ����� ���� ������������ ������ (json ��� xml, �� ������ ����������� ����������)
	2. �����������
	� ������ � API ������ ��������������� ����������� ����������
	� ��������� �������� 404� � 500� ������
	� �������� ������������ ��������� (development, production)
	�������������� �������:
	�����������, ��� �������� �������� �� ���������� �������� 5 000 000 �����.
	������� �������������� ��������� � ����������� �������, �������
	����� ������������, ����� ��������� �������� ��������.
	������� ������� ������ ���� � ���� ����������� �� github, � ����������� �� �������
	� ���������. ���� ���������������� ��� ���������� ������� - PHP.
	
	---
	� ������ ������� API ����������� ������������ ����� �� + ����������� (�� ����� ������� ��������� � index.php)
	�������������� ������ ����������� � ����������� UserList
	---
	������ ������� ����� ���� ��� � _GET, ��� � _POST (������������ ������� ��� �������)
	---
	
	��������� API
	---
	'format' - ������ ������ ������ (html, json, xml)
	'authorization' - ������ ��� �����������, ������ ('login'=>'', 'pass'=>''),
	�������� ������ - test \ test
	���� ��������������� �� 1 ���
	'action' - ������� ��������:
		'gettable' - �������� ������� ��������� ������� � ��������� � ������� �����
		'get' - �������� ������ ������������� � ������������ � ����������� � ���������� ������
		'getbyid' - �������� ������ ������������ �� id
		'add' - ��������� ������������ � ������� �� data (������ ���� '����'=>'��������')
		'update' - ��������� ������ ������������ �� id ������� �� data (������ ���� '����'=>'��������')
		'delete' - ������� ������������ �� id
	---
	������ � ����� ���������� �������� � ������ Route
	������ � ����� ����� ������� �������� � ������ UserModel
	---
	
	��������� ������ �������
	---
	index.php - ����� ����� ����������
	route.php - ����� ������������� � ����������� (����������)
	core.php  - �����, ���������� ������ ������ � ��
	db.php    - ����� ������ � �� (PDO)
	formatter.php - ������ �������������� ������
	-
	�������������� �����
	tests.php - ����� ������
	users.sql - sql-���� ������� users � ���� users, ����������� ��� ������.
	
	������ ������
	---
	� index.php ��������� ��������� ������-Singleton Route (route.php), ������� ��������� �����������,
	������������ ������� ��������� (GET ��� POST � ����������� �� �������).
	Route � ����������� �� ��������� action ��������� ����������� ��������� �� �������,
	���� ��� � ������� - ������� ��������� UserModel (core.php) � �������� �����, ��������������� action.
	������������ �������� - ������ ������-���������� DefaultFormatter (formatter.php), ����������� ���������
	����� ��� �������������, ����� ���������� ������ �������������� �� ��������.
	� ����������� ������� ���������� �����, �������������� ����������� � ��������� format.
	���������� ������ ���������.
	������ �������������� �� ������ Route, ��� ���� ��� ������������� � ������������ � �������� ������.
	������������ ������ � ���� �������� ������ - ������ "�������� ������ ������".
	
	���������
	---
	����������� ����������� � ����� web-�������, ������� � �� MySQL ���� 'users'
	��������� ������������, �������� ������ � ������ �� � ��� ������ � ����� db.php �������
	
	private $user = "";
	private $pass = "";	
	
	������������� �� PHP 5.3
	
	
	
	