<?php
/**
Implements operations on a Minimum Bounding Rectangle (MBR).  A MBR is, by definition, only 2-D.  You are responsible
for ensuring that the given coordinates are all in the same coordinate system.
*/
class GIS_MBR {
    /* public API */
    // {{{ __construct()

    public function __construct(/* ... */) {
        $args = func_get_args();

        // if instantiated with dimensions
        if (4 === count($args)) {
            list ($minx, $miny, $maxx, $maxy) = $args;
            if (! is_numeric($minx)) {
                throw new InvalidArgumentException('Minimum X must be numeric');
            }
            if (! is_numeric($miny)) {
                throw new InvalidArgumentException('Minimum Y must be numeric');
            }
            if (! is_numeric($maxx)) {
                throw new InvalidArgumentException('Maximum X must be numeric');
            }
            if (! is_numeric($maxy)) {
                throw new InvalidArgumentException('Maximum Y must be numeric');
            }

            $this->minx = $minx;
            $this->miny = $miny;
            $this->maxx = $maxx;
            $this->maxy = $maxy;

        } else if (0 !== count($args)) {
            // nothing to do
            throw new InvalidArgumentException('Instantiate with no arguments for a dimensionless MBR, or with exactly 4 dimensions');
        }

    }

    // }}}
    // {{{ addMBR()

    /**
    Add another MBR to this one, to get the resulting MBR.  Expands this MBR iff the given MBR exceeds this one in at
    least one dimension.
    */
    public function addMBR(GIS_MBR $MBR) {
        // add the minimum and maximum points
        $this->addPoint($MBR->minx, $MBR->miny);
        $this->addPoint($MBR->maxx, $MBR->maxy);
        return $this;
    }

    // }}}
    // {{{ addPoint()

    /**
    Add another point to this MBR, expanding the MBR as necessary.  Expands this MBR iff the point lies outside this MBR.
    */
    public function addPoint($x, $y) {
        // if the MBR has no dimension, initialize to this point
        if (null === $this->minx) {
            $this->minx = $this->maxx = $x;
            $this->miny = $this->maxy = $y;

        // otherwise, add this point in
        } else {
            // if the x-coordinate is outside our current minimum or maximum, expand
            if ($x < $this->minx) {
                $this->minx = $x;
            } else if ($this->maxx < $x) {
                $this->maxx = $x;
            }

            // same thing, but for y-coordinate
            if ($y < $this->miny) {
                $this->miny = $y;
            } else if ($this->maxy < $y) {
                $this->maxy = $y;
            }
        }

        return $this;
    }

    // }}}
    // {{{ getMinX()

    /**
    Get the minimum X coordinate.
    */
    public function getMinX() {
        return $this->minx;
    }

    // }}}
    // {{{ getMinY()

    /**
    Get the minimum Y coordinate.
    */
    public function getMinY() {
        return $this->miny;
    }

    // }}}
    // {{{ getMaxX()

    /**
    Get the maximum X coordinate.
    */
    public function getMaxX() {
        return $this->maxx;
    }

    // }}}
    // {{{ getMaxY()

    /**
    Get the maximum Y coordinate.
    */
    public function getMaxY() {
        return $this->maxy;
    }

    // }}}

    /* protected API */
    // {{{ $minx

    /**
    The minimum X coordinate of the MBR
    */
    protected $minx = null;

    // }}}
    // {{{ $miny

    /**
    The minimum Y coordinate of the MBR
    */
    protected $miny = null;

    // }}}
    // {{{ $maxx

    /**
    The maximum X coordinate of the MBR
    */
    protected $maxx = null;

    // }}}
    // {{{ $maxy

    /**
    The maximum Y coordinate of the MBR
    */
    protected $maxy = null;

    // }}}
}

?>
