#include <Python.h>

// definition in C of golang function
long IsValid(char *);


static PyObject *
is_valid(PyObject *self, PyObject *args)
{
    char * source;
    long res;

    if (!PyArg_ParseTuple(args, "s", &source))
        return NULL;

    res = IsValid(source);
    return PyBool_FromLong(res);
}


static PyMethodDef SPDXMethods[] = {
    {"is_valid", is_valid, METH_VARARGS, "Check if the given license is valid."},
    {NULL, NULL, 0, NULL}
};

PyMODINIT_FUNC
initspdx(void)
{
    (void) Py_InitModule("spdx", SPDXMethods);
}
