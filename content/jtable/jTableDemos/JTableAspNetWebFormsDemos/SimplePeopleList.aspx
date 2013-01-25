<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="SimplePeopleList.aspx.cs"
    Inherits="jTableWithAspNetWebForms.SimplePeopleList" %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>Simple Person List With jTable and ASP.NET Web Forms</title>
    <link href="/Content/Site.css" rel="stylesheet" type="text/css" />
    <link href="/Content/themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet"
        type="text/css" />
    <!-- jTable style file -->
    <link href="/Scripts/jtable/themes/standard/blue/jtable_blue.css" rel="stylesheet"
        type="text/css" />
    <script src="/Scripts/jquery-1.6.4.min.js" type="text/javascript"></script>
    <script src="/Scripts/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
    <script src="/Scripts/modernizr-1.7.min.js" type="text/javascript"></script>
    <script src="/Scripts/jtablesite.js" type="text/javascript"></script>
    <!-- A helper library for JSON serialization -->
    <script type="text/javascript" src="/Scripts/jtable/external/json2.js"></script>
    <!-- Core jTable script file -->
    <script type="text/javascript" src="/Scripts/jtable/jquery.jtable.js"></script>
    <!-- ASP.NET Web Forms extension for jTable -->
    <script type="text/javascript" src="/Scripts/jtable/extensions/jquery.jtable.aspnetpagemethods.js"></script>
</head>
<body>
    <div id="PersonTableContainer">
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#PersonTableContainer').jtable({
                title: 'Table of people',
                actions: {
                    listAction: '/SimplePeopleList.aspx/PersonList',
                    createAction: '/SimplePeopleList.aspx/CreatePerson',
                    updateAction: '/SimplePeopleList.aspx/UpdatePerson',
                    deleteAction: '/SimplePeopleList.aspx/DeletePerson'
                },
                fields: {
                    PersonId: {
                        key: true,
                        create: false,
                        edit: false,
                        list: false
                    },
                    Name: {
                        title: 'Author Name',
                        width: '40%'
                    },
                    Age: {
                        title: 'Age',
                        width: '20%'
                    },
                    RecordDate: {
                        title: 'Record date',
                        width: '30%',
                        type: 'date',
                        create: false,
                        edit: false
                    }
                }
            });

            $('#PersonTableContainer').jtable('load');
        });
    </script>
</body>
</html>
