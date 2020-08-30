INSERT INTO permissions(permission, label, `level`, `group`) values('create_wristbands', 'create_wristbands', 1, 23);
INSERT INTO permissions(permission, label, `level`, `group`) values('show_wristbands', 'show_wristbands', 1, 23);
INSERT INTO permissions(permission, label, `level`, `group`) values('delete_wristbands', 'delete_wristbands', 1, 23);
INSERT INTO permissions(permission, label, `level`, `group`) values('edit_wristbands', 'edit_wristbands', 1, 23);

INSERT INTO permissions(permission, label, `level`, `group`) values('create_wristband_passes', 'create_wristband_passes', 1, 24);
INSERT INTO permissions(permission, label, `level`, `group`) values('edit_wristband_passes', 'edit_wristband_passes', 1, 24);
INSERT INTO permissions(permission, label, `level`, `group`) values('show_wristband_passes', 'show_wristband_passes', 1, 24);
INSERT INTO permissions(permission, label, `level`, `group`) values('delete_wristband_passes', 'delete_wristband_passes', 1, 24);

INSERT INTO permissions(permission, label, `level`, `group`) values('create_pack', 'create_pack', 1, 25);
INSERT INTO permissions(permission, label, `level`, `group`) values('show_pack', 'show_pack', 1, 25);
INSERT INTO permissions(permission, label, `level`, `group`) values('edit_pack', 'edit_pack', 1, 25);
INSERT INTO permissions(permission, label, `level`, `group`) values('delete_pack', 'delete_pack', 1, 25);

INSERT INTO permissions(permission, label, `level`, `group`) values('download_payment', 'download_payment', 1, 22);

/* insert permission into role*/
INSERT INTO role_permissions(role_id, permission_id) select roles.id as role_id,  permissions.id as permission_id from permissions, roles where roles.id = 1;