package main

import "github.com/snapcore/snapd/spdx"

// #cgo pkg-config: python2
import "C"

//export IsValid
func IsValid(license *C.char) bool {
    res := spdx.ValidateLicense(C.GoString(license))
    if res == nil {
		return true
	} else {
	    return false
	}
}

func main() {}
