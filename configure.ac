# -*- Autoconf -*-
# Process this file with autoconf to produce a configure script.

AC_PREREQ([2.63])
AC_INIT(MosaicAlbum, 1.0.0)

AC_CONFIG_SRCDIR([.dbup/properties.ini.in])
AC_CONFIG_SRCDIR([src/Vg/Repository/FBHelperRepository.php.in])

AC_ARG_ENABLE(production,
    [--enable-production 本番環境の設定([[default=no]])],
    [\
        case "${enableval}" in
        yes) enable_production=yes ;;
        no) enable_production=no ;;
        *) enable_production=yes ;;
    esac],
    enable_production=no
)
if test x"$enable_production" = x"yes"; then
    DB_NAME="groupwork"
    HOST="localhost"
    USER_NAME="demouser"
    USER_PASS="demopass"
	DEV=""
fi
if test x"$enable_production" = x"no"; then
    DB_NAME="groupwork"
    HOST="localhost"
    USER_NAME="demouser"
    USER_PASS="demopass"
	DEV="dev."
fi

AC_SUBST(DB_NAME)
AC_SUBST(HOST)
AC_SUBST(USER_NAME)
AC_SUBST(USER_PASS)
AC_SUBST(DEV)

AC_CONFIG_FILES([.dbup/properties.ini])
AC_CONFIG_FILES([src/Vg/Repository/FBHelperRepository.php])
AC_OUTPUT
