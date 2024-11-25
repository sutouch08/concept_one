<style>
.freez > th {
  top:0;
  position: sticky;
  background-color: white;
  outline: solid 1px #dddddd;
  min-height: 30px;
  height: 30px;
}

.tableFixHead {
  table-layout: fixed;
  min-width: 100%;
  /* width:1780px; */
  margin-top:-1px;
  margin-left:-1px;
  margin-right:0px;
  margin-bottom: 0;
}

.tableFixHead thead th {
  position: sticky;
  top: -1px;
  background: #eee;
}

.tableFixHead tr:first-child {
  top: -1px;
}

.tableFixHead tr > td {
  padding: 3px !important;
}

select.input-xs {
  padding: 0px 6px;
  border-radius: 0;
}

td > select.input-xs {
  border:none;
}

td > input.input-xs {
  border:none;
}

td > input.input-xs:disabled {
  background-color: white !important;
  color: #555555 !important;
}

.form-group {
  margin-bottom:5px !important;
}

.header-row label {
  font-size:11px;
}
</style>
