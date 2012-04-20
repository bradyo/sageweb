<?php

class Sageweb_Cms_Table_Comment
{
    private static function _getRootQuery()
    {
        return Doctrine_Query::create()
            ->from('Sageweb_Cms_PostComment p, p.entity e, p.author a');
    }

    public static function findOneById($id)
    {
        return self::_getRootQuery()
            ->where('p.id = ?', $id)
            ->limit(1)
            ->fetchOne();
    }

    /**
     * Gets the article with the given id.
     * @param integer $id
     * @return Sageweb_Cms_PostComment
     */
    public static function findOneByEntityId($entityId)
    {
        $query = self::_getRootQuery();
        $query->where('p.entityId = ?', $entityId);
        $query->limit(1);
        return $query->fetchOne();
    }

    public static function findRecent($entityId)
    {
        $comments = Doctrine_Query::create()->from('Sageweb_Cms_PostComment p')
            ->where('p.rootEntityId = ?', $entityId)
            ->orderBy('p.createdAt DESC')
            ->execute();
        return $comments;
    }

    public static function getNestedTree($entityId, $maxDepth = null)
    {
        $comments = Doctrine_Query::create()->from('Sageweb_Cms_PostComment p')
            ->where('p.rootEntityId = ?', $entityId)
            ->andWhere('p.status = ? OR p.status = ?',
                array(Sageweb_Cms_PostComment::STATUS_PUBLIC, Sageweb_Cms_PostComment::STATUS_PENDING)
                )
            ->orderBy('p.createdAt ASC')
            ->execute();
        $tree = self::_buildTree($comments, $entityId, 1, $maxDepth);
        return $tree;
    }

    private static function _buildTree($comments, $parentEntityId, $depth, $maxDepth = null)
    {
        if ($maxDepth !== null && $depth > $maxDepth) {
            return array();
        }

        $tree = array();
        foreach ($comments as $comment) {
            if ($comment->parentEntityId == $parentEntityId) {
                $comment->setDepth($depth);
                $children = self::_buildTree($comments, $comment->entityId, $depth + 1, $maxDepth);
                $tree[] = array(
                    'comment' => $comment,
                    'children' => $children
                );
            }
        }
        return $tree;
    }
}
